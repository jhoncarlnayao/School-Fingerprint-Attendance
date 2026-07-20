<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\AnnouncementMail;
use App\Models\ActivityLog;
use App\Models\Announcement;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Throwable;

class AnnouncementController extends Controller
{
    /**
     * List all announcements.
     */
    public function index()
    {
        $announcements = Announcement::with('author')->latest()->get();

        return view('admin.announcements.index', compact('announcements'));
    }

    /**
     * Show the "Post Announcement" form.
     * This single form now both posts the announcement to the board AND
     * emails it (via SMTP) straight to the registered inboxes of the
     * chosen audience — there is no separate "Email Announcement" step.
     */
    public function create()
    {
        return view('admin.announcements.create');
    }

    /**
     * Save a new announcement and immediately email it out over SMTP
     * to the registered email addresses of the chosen audience.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'in:all,teachers,students'],
        ]);

        $announcement = Announcement::create($validated + ['created_by' => $request->user()->id]);

        // Recipients are pulled from the *registered* user accounts — this is
        // the same email an admin typed in on "Register Teacher", so there is
        // nothing extra to configure per-announcement. Students/guardians
        // don't have login emails on file yet, so that audience is posted
        // to the board only (matches the note shown on the form).
        $recipients = match ($validated['audience']) {
            'teachers' => User::where('role', 'teacher')->pluck('email'),
            'all' => User::whereIn('role', ['admin', 'teacher'])->pluck('email'),
            default => collect(),
        };

        $sent = 0;
        $mailError = null;

        foreach ($recipients as $email) {
            try {
                Mail::to($email)->send(new AnnouncementMail($announcement));
                $sent++;
            } catch (Throwable $e) {
                // Keep posting even if SMTP isn't configured yet — just surface it once.
                $mailError = $e->getMessage();
            }
        }

        if ($sent > 0) {
            $announcement->update(['emailed' => true]);
        }

        ActivityLog::record(
            'created',
            'Announcement',
            $announcement->id,
            auth()->user()->name." posted the announcement \"{$announcement->title}\"".($sent > 0 ? " and emailed it to {$sent} recipient(s)." : '.')
        );

        if ($mailError && $sent === 0 && $recipients->count() > 0) {
            return redirect()->route('admin.announcements.index')
                ->with('status', 'Announcement posted, but the email could not be sent — check your MAIL_* settings in .env.')
                ->with('mail_error', $mailError);
        }

        $statusMessage = $sent > 0
            ? "Announcement posted and emailed to {$sent} recipient(s)."
            : 'Announcement posted.';

        return redirect()->route('admin.announcements.index')->with('status', $statusMessage);
    }

    /**
     * Update an existing announcement's title, audience, or message
     * (used by the "Edit" button/modal on the Announcements list).
     * This only edits the posted content — it does not re-send emails.
     */
    public function update(Request $request, Announcement $announcement): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'audience' => ['required', 'in:all,teachers,students'],
        ]);

        $announcement->update($validated);

        ActivityLog::record(
            'updated',
            'Announcement',
            $announcement->id,
            auth()->user()->name." edited the announcement \"{$announcement->title}\"."
        );

        return redirect()->route('admin.announcements.index')->with('status', 'Announcement updated.');
    }
}
