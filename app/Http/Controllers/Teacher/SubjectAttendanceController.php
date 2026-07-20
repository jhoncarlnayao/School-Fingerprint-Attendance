<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Jobs\SendGuardianSubjectAttendanceEmail;
use App\Models\ActivityLog;
use App\Models\Student;
use App\Models\SubjectAttendanceLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class SubjectAttendanceController extends Controller
{
    /**
     * Roster for the teacher's assigned subject/section, for a given date
     * (defaults to today), so they can quickly check who's present in
     * their class and notify guardians in one submit.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();

        if (! $teacher->assigned_grade_level || ! $teacher->assigned_section || ! $teacher->assigned_subject) {
            return view('teacher.subject-attendance.index', [
                'teacher' => $teacher,
                'unassigned' => true,
                'date' => Carbon::today(),
            ]);
        }

        $date = $request->filled('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $students = Student::where('grade_level', $teacher->assigned_grade_level)
            ->where('section', $teacher->assigned_section)
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        $logs = SubjectAttendanceLog::where('teacher_id', $teacher->id)
            ->whereIn('student_id', $students->pluck('id'))
            ->forDate($date)
            ->get();

        $roster = $students->map(fn (Student $student) => [
            'student' => $student,
            'log' => $logs->firstWhere('student_id', $student->id),
        ]);

        $present = $logs->where('status', SubjectAttendanceLog::STATUS_PRESENT)->count();
        $late = $logs->where('status', SubjectAttendanceLog::STATUS_LATE)->count();
        $absent = $logs->where('status', SubjectAttendanceLog::STATUS_ABSENT)->count();
        $notMarked = max($students->count() - $logs->count(), 0);

        return view('teacher.subject-attendance.index', compact(
            'teacher', 'date', 'roster', 'present', 'late', 'absent', 'notMarked'
        ));
    }

    /**
     * Save the whole roster's statuses for this subject/date in one submit,
     * then queue guardian emails for anyone marked present/late.
     */
    public function store(Request $request): RedirectResponse
    {
        $teacher = auth()->user();

        if (! $teacher->assigned_grade_level || ! $teacher->assigned_section || ! $teacher->assigned_subject) {
            return back()->with('warning', 'You are not yet assigned to a section and subject.');
        }

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'statuses' => ['required', 'array'],
            'statuses.*' => [Rule::in([
                SubjectAttendanceLog::STATUS_PRESENT,
                SubjectAttendanceLog::STATUS_LATE,
                SubjectAttendanceLog::STATUS_ABSENT,
                SubjectAttendanceLog::STATUS_EXCUSED,
            ])],
        ]);

        $studentIds = Student::where('grade_level', $teacher->assigned_grade_level)
            ->where('section', $teacher->assigned_section)
            ->pluck('id');

        $notified = 0;

        foreach ($validated['statuses'] as $studentId => $status) {
            if (! $studentIds->contains((int) $studentId)) {
                continue; // ignore anything outside the teacher's own section
            }

            $log = SubjectAttendanceLog::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'teacher_id' => $teacher->id,
                    'date' => $validated['date'],
                ],
                [
                    'subject' => $teacher->assigned_subject,
                    'grade_level' => $teacher->assigned_grade_level,
                    'section' => $teacher->assigned_section,
                    'status' => $status,
                ]
            );

            if ($log->shouldNotifyGuardian() && ! $log->guardian_notified_at && $log->student->guardian_email) {
                SendGuardianSubjectAttendanceEmail::dispatch($log->id);
                $notified++;
            }
        }

        ActivityLog::record(
            'updated',
            'SubjectAttendanceLog',
            null,
            "{$teacher->name} (teacher) recorded {$teacher->assigned_subject} attendance for ".count($validated['statuses'])." student(s) on ".Carbon::parse($validated['date'])->format('M d, Y').'.'
        );

        return redirect()
            ->route('teacher.subject-attendance.index', ['date' => $validated['date']])
            ->with('status', 'Attendance saved. '.($notified > 0 ? "{$notified} guardian(s) notified by email." : 'No new guardian emails needed.'));
    }

    /**
     * Per-student subject attendance history — only for students inside the
     * teacher's own assigned section.
     */
    public function show(Request $request, Student $student)
    {
        $teacher = auth()->user();

        if (
            $student->grade_level !== $teacher->assigned_grade_level
            || $student->section !== $teacher->assigned_section
        ) {
            abort(403, 'You can only view attendance for students in your own section.');
        }

        $logs = SubjectAttendanceLog::where('student_id', $student->id)
            ->where('teacher_id', $teacher->id)
            ->orderByDesc('date')
            ->paginate(31)
            ->withQueryString();

        $summary = [
            'present' => SubjectAttendanceLog::where('student_id', $student->id)->where('teacher_id', $teacher->id)->where('status', SubjectAttendanceLog::STATUS_PRESENT)->count(),
            'late' => SubjectAttendanceLog::where('student_id', $student->id)->where('teacher_id', $teacher->id)->where('status', SubjectAttendanceLog::STATUS_LATE)->count(),
            'absent' => SubjectAttendanceLog::where('student_id', $student->id)->where('teacher_id', $teacher->id)->where('status', SubjectAttendanceLog::STATUS_ABSENT)->count(),
            'excused' => SubjectAttendanceLog::where('student_id', $student->id)->where('teacher_id', $teacher->id)->where('status', SubjectAttendanceLog::STATUS_EXCUSED)->count(),
        ];

        return view('teacher.subject-attendance.show', compact('student', 'logs', 'summary', 'teacher'));
    }
}
