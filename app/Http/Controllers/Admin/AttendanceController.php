<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\AttendanceLog;
use App\Models\Section;
use App\Models\Student;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class AttendanceController extends Controller
{
    /**
     * Attendance dashboard: today's present/late/absent counts, plus a
     * per-section breakdown so an admin can see at a glance which sections
     * still have unscanned/absent students.
     */
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $totalStudents = Student::count();
        $logs = AttendanceLog::with('student')->forDate($date)->get();

        $present = $logs->where('status', AttendanceLog::STATUS_PRESENT)->count();
        $late = $logs->where('status', AttendanceLog::STATUS_LATE)->count();
        $absent = $logs->where('status', AttendanceLog::STATUS_ABSENT)->count();
        $excused = $logs->where('status', AttendanceLog::STATUS_EXCUSED)->count();
        $notYetScanned = max($totalStudents - $logs->count(), 0);

        $sections = Section::orderBy('grade_level')->orderBy('name')->get()->map(function (Section $section) use ($logs) {
            $studentIds = Student::where('grade_level', $section->grade_level)
                ->where('section', $section->name)
                ->pluck('id');

            $sectionLogs = $logs->whereIn('student_id', $studentIds);

            return [
                'label' => $section->label(),
                'total' => $studentIds->count(),
                'present' => $sectionLogs->whereIn('status', [AttendanceLog::STATUS_PRESENT, AttendanceLog::STATUS_LATE])->count(),
                'late' => $sectionLogs->where('status', AttendanceLog::STATUS_LATE)->count(),
                'absent' => $sectionLogs->where('status', AttendanceLog::STATUS_ABSENT)->count(),
                'not_scanned' => max($studentIds->count() - $sectionLogs->count(), 0),
            ];
        })->filter(fn ($row) => $row['total'] > 0)->values();

        // Students with no log yet today, for the "manual override" quick list.
        $unscannedStudents = Student::whereNotIn('id', $logs->pluck('student_id'))
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get();

        return view('admin.attendance.index', compact(
            'date',
            'totalStudents',
            'present',
            'late',
            'absent',
            'excused',
            'notYetScanned',
            'sections',
            'logs',
            'unscannedStudents'
        ));
    }

    /**
     * Per-student attendance history.
     */
    public function show(Request $request, Student $student)
    {
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

        return view('admin.attendance.show', compact('student', 'logs', 'summary'));
    }

    /**
     * Export a student's full attendance history as CSV (e.g. for SF2-style
     * DepEd reporting or report cards).
     */
    public function exportCsv(Student $student)
    {
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
     * used when the scanner is offline, a student forgot to scan, or an
     * absence needs to be excused.
     */
    public function markManual(Request $request, Student $student): RedirectResponse
    {
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
            auth()->user()->name." manually marked {$student->fullName()} as \"{$log->statusLabel()}\" for {$log->date->format('M d, Y')}."
        );

        return back()->with('status', "{$student->fullName()}'s attendance for {$log->date->format('M d, Y')} was set to \"{$log->statusLabel()}\".");
    }
}
