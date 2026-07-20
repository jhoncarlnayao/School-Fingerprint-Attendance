<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Section;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class TeacherController extends Controller
{
    /**
     * Show the "Register Teacher" form.
     * Admin-only route — enforced by the `role:admin` middleware in routes/web.php.
     */
    public function register()
    {
        $teachers = User::where('role', 'teacher')->latest()->get();

        return view('admin.teachers.register', compact('teachers'));
    }

    /**
     * Create a new teacher account.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $fullName = trim("{$validated['first_name']} {$validated['middle_name']} {$validated['last_name']}");
        $fullName = preg_replace('/\s+/', ' ', $fullName);

        $profilePicturePath = $request->hasFile('profile_picture')
            ? $request->file('profile_picture')->store('profiles/teachers', 'public')
            : null;

        $teacher = User::create([
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'teacher',
            'profile_picture' => $profilePicturePath,
            'is_active' => true,
        ]);

        ActivityLog::record(
            'created',
            'Teacher',
            $teacher->id,
            auth()->user()->name." registered a new teacher account for {$fullName} ({$validated['email']})."
        );

        return back()->with('status', "Teacher account for {$fullName} was created.");
    }

    /**
     * Update a teacher's own profile info (name, birthday, email, photo).
     * Used by the "Edit" button/modal on the Register Teacher page.
     */
    public function update(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:100'],
            'middle_name' => ['nullable', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'birth_date' => ['nullable', 'date', 'before:today'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users', 'email')->ignore($teacher->id)],
            'profile_picture' => ['nullable', 'image', 'max:2048'],
        ]);

        $fullName = trim("{$validated['first_name']} {$validated['middle_name']} {$validated['last_name']}");
        $fullName = preg_replace('/\s+/', ' ', $fullName);

        $updateData = [
            'name' => $fullName,
            'first_name' => $validated['first_name'],
            'middle_name' => $validated['middle_name'] ?? null,
            'last_name' => $validated['last_name'],
            'birth_date' => $validated['birth_date'] ?? null,
            'email' => $validated['email'],
        ];

        if ($request->hasFile('profile_picture')) {
            $updateData['profile_picture'] = $request->file('profile_picture')->store('profiles/teachers', 'public');
        }

        $teacher->update($updateData);

        ActivityLog::record(
            'updated',
            'Teacher',
            $teacher->id,
            auth()->user()->name." updated {$fullName}'s profile information."
        );

        return back()->with('status', "{$fullName}'s information was updated.");
    }

    /**
     * Show the "Assign Teacher" page — set a teacher's grade level, section & subject.
     * Sections must already exist (admin creates them on the Sections page first).
     */
    public function assign()
    {
        $teachers = User::where('role', 'teacher')->orderBy('name')->get();
        $sections = Section::orderBy('grade_level')->orderBy('name')->get();
        $gradeLevels = Section::gradeLevels();

        return view('admin.teachers.assign', compact('teachers', 'sections', 'gradeLevels'));
    }

    /**
     * Update a teacher's assigned grade level/section/subject.
     *
     * If another teacher already covers the exact same grade level, section,
     * and subject, we still save the change (an admin might genuinely want a
     * co-teacher) but flash a warning notice and log it so it shows up on
     * the notification bell / activity log for visibility.
     */
    public function assignUpdate(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $validated = $request->validate([
            'assigned_grade_level' => ['nullable', Rule::in(Section::gradeLevels())],
            'assigned_section' => [
                'nullable',
                'string',
                'max:100',
                Rule::exists('sections', 'name')->where(fn ($q) => $q->where('grade_level', $request->assigned_grade_level)),
            ],
            'assigned_subject' => ['nullable', 'string', 'max:100'],
        ]);

        $conflict = null;

        if (! empty($validated['assigned_grade_level']) && ! empty($validated['assigned_section']) && ! empty($validated['assigned_subject'])) {
            $conflict = User::where('role', 'teacher')
                ->where('id', '!=', $teacher->id)
                ->where('assigned_grade_level', $validated['assigned_grade_level'])
                ->where('assigned_section', $validated['assigned_section'])
                ->whereRaw('LOWER(assigned_subject) = ?', [strtolower($validated['assigned_subject'])])
                ->first();
        }

        $teacher->update($validated);

        $sectionLabel = $validated['assigned_grade_level'] && $validated['assigned_section']
            ? "Grade {$validated['assigned_grade_level']} - {$validated['assigned_section']}"
            : null;

        if ($conflict) {
            $warningMessage = "\"{$sectionLabel}\" already has {$conflict->name} teaching {$validated['assigned_subject']}. {$teacher->name} was assigned anyway — you may want to review this.";

            ActivityLog::record(
                'assigned',
                'Teacher',
                $teacher->id,
                auth()->user()->name." assigned {$teacher->name} to {$sectionLabel} ({$validated['assigned_subject']}) — duplicate subject teacher warning: also taught by {$conflict->name}.",
                true
            );

            return back()->with('status', "{$teacher->name} has been updated.")->with('warning', $warningMessage);
        }

        ActivityLog::record(
            'assigned',
            'Teacher',
            $teacher->id,
            auth()->user()->name." updated {$teacher->name}'s assignment".($sectionLabel ? " to {$sectionLabel} ({$validated['assigned_subject']})." : ' (cleared).')
        );

        return back()->with('status', "{$teacher->name} has been updated.");
    }

    /**
     * Disable a teacher account. A reason is required — it will be shown
     * back to the teacher on the login page if they try to sign in while
     * disabled.
     */
    public function disable(Request $request, User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $validated = $request->validate([
            'disabled_reason' => ['required', 'string', 'max:500'],
        ]);

        $teacher->update([
            'is_active' => false,
            'disabled_reason' => $validated['disabled_reason'],
            'disabled_at' => now(),
        ]);

        ActivityLog::record(
            'disabled',
            'Teacher',
            $teacher->id,
            \Illuminate\Support\Str::limit(
                auth()->user()->name." disabled {$teacher->name}'s account. Reason: {$validated['disabled_reason']}",
                490
            ),
            true
        );

        return back()->with('status', "{$teacher->name}'s account has been disabled.");
    }

    /**
     * Re-enable a previously disabled teacher account.
     */
    public function enable(User $teacher): RedirectResponse
    {
        abort_unless($teacher->role === 'teacher', 404);

        $teacher->update([
            'is_active' => true,
            'disabled_reason' => null,
            'disabled_at' => null,
        ]);

        ActivityLog::record(
            'enabled',
            'Teacher',
            $teacher->id,
            auth()->user()->name." re-enabled {$teacher->name}'s account."
        );

        return back()->with('status', "{$teacher->name}'s account has been re-enabled.");
    }
}
