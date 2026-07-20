<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Attendance dashboard scoped to the logged-in teacher's own section only.
     */
    public function index(Request $request)
    {
        $teacher = auth()->user();

        if (! $teacher->assigned_grade_level || ! $teacher->assigned_section) {
            return view('teacher.attendance.index', [
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

        $logs = AttendanceLog::with('student')
            ->whereIn('student_id', $students->pluck('id'))
            ->forDate($date)
            ->get();

        $present = $logs->where('status', AttendanceLog::STATUS_PRESENT)->count();
        $late = $logs->where('status', AttendanceLog::STATUS_LATE)->count();
        $absent = $logs->where('status', AttendanceLog::STATUS_ABSENT)->count();
        $excused = $logs->where('status', AttendanceLog::STATUS_EXCUSED)->count();
        $notYetScanned = max($students->count() - $logs->count(), 0);

        // Merge each student with today's log (if any) for the roster table.
        $roster = $students->map(function (Student $student) use ($logs) {
            return [
                'student' => $student,
                'log' => $logs->firstWhere('student_id', $student->id),
            ];
        });

        $unscannedStudents = $students->whereNotIn('id', $logs->pluck('student_id'))->values();

        return view('teacher.attendance.index', compact(
            'teacher',
            'date',
            'present',
            'late',
            'absent',
            'excused',
            'notYetScanned',
            'roster',
            'unscannedStudents'
        ));
    }

    /**
     * Per-student attendance history — only for students inside the
     * teacher's own assigned section.
     */
    public function show(Request $request, Student $student)
    {
        $this->authorizeStudent($student);

        $query = $student->attendanceLogs()->orderByDesc('date');

        if ($request->filled('from')) {
            $query->whereDate('date', '>=', $request->query('from'));
        }
        if ($request->filled('to')) {
            $query->whereDate('date', '<=', $request->query('to'));
        }

        $logs = $query->paginate(31)->withQueryString();

        $summary = [
            'present' => $student->attendanceLogs()->where('status', AttendanceLog::STATUS_PRESENT)->count(),
            'late' => $student->attendanceLogs()->where('status', AttendanceLog::STATUS_LATE)->count(),
            'absent' => $student->attendanceLogs()->where('status', AttendanceLog::STATUS_ABSENT)->count(),
            'excused' => $student->attendanceLogs()->where('status', AttendanceLog::STATUS_EXCUSED)->count(),
        ];

        return view('teacher.attendance.show', compact('student', 'logs', 'summary'));
    }

    /**
     * Export a student's full attendance history as CSV — same as the admin
     * export, but only reachable for students in the teacher's own section.
     */
    public function exportCsv(Student $student)
    {
        $this->authorizeStudent($student);

        $logs = $student->attendanceLogs()->orderBy('date')->get();

        $filename = str($student->fullName().'-attendance')->slug().'.csv';

        return Response::streamDownload(function () use ($logs) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Date', 'Time In', 'Time Out', 'Status', 'Source', 'Note']);

            foreach ($logs as $log) {
                fputcsv($out, [
                    $log->date->format('Y-m-d'),
                    $log->timeInFormatted() ?? '',
                    $log->timeOutFormatted() ?? '',
                    $log->statusLabel(),
                    ucfirst($log->source),
                    $log->note ?? '',
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    /**
     * Manually mark/override a student's attendance for a given date —
     * only allowed for students inside the teacher's own assigned section.
     */
    public function markManual(Request $request, Student $student): RedirectResponse
    {
        $this->authorizeStudent($student);

        $validated = $request->validate([
            'date' => ['required', 'date'],
            'status' => ['required', Rule::in([
                AttendanceLog::STATUS_PRESENT,
                AttendanceLog::STATUS_LATE,
                AttendanceLog::STATUS_ABSENT,
                AttendanceLog::STATUS_EXCUSED,
            ])],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $log = AttendanceLog::updateOrCreate(
            ['student_id' => $student->id, 'date' => $validated['date']],
            [
                'status' => $validated['status'],
                'source' => AttendanceLog::SOURCE_MANUAL,
                'note' => $validated['note'] ?? null,
                'marked_by' => auth()->id(),
            ]
        );

        ActivityLog::record(
            'updated',
            'AttendanceLog',
            $log->id,
            auth()->user()->name." (teacher) manually marked {$student->fullName()} as \"{$log->statusLabel()}\" for {$log->date->format('M d, Y')}."
        );

        return back()->with('status', "{$student->fullName()}'s attendance for {$log->date->format('M d, Y')} was set to \"{$log->statusLabel()}\".");
    }

    /**
     * Guard against a teacher viewing/editing attendance for a student
     * outside their own assigned grade level + section.
     */
    protected function authorizeStudent(Student $student): void
    {
        $teacher = auth()->user();

        if (
            $student->grade_level !== $teacher->assigned_grade_level
            || $student->section !== $teacher->assigned_section
        ) {
            abort(403, 'You can only view attendance for students in your own section.');
        }
    }
}
