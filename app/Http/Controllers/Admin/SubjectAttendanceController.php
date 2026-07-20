<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SubjectAttendanceLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class SubjectAttendanceController extends Controller
{
    /**
     * Read-only, school-wide view of subject-level attendance taken by
     * teachers — filterable by date and by teacher.
     */
    public function index(Request $request)
    {
        $date = $request->filled('date') ? Carbon::parse($request->query('date')) : Carbon::today();

        $query = SubjectAttendanceLog::with(['student', 'teacher'])->forDate($date);

        if ($request->filled('teacher_id')) {
            $query->where('teacher_id', $request->query('teacher_id'));
        }

        $logs = $query->orderBy('section')->orderBy('subject')
            ->get()
            ->sortBy(fn ($log) => $log->student->fullName());

        $present = $logs->where('status', SubjectAttendanceLog::STATUS_PRESENT)->count();
        $late = $logs->where('status', SubjectAttendanceLog::STATUS_LATE)->count();
        $absent = $logs->whereIn('status', [SubjectAttendanceLog::STATUS_ABSENT, SubjectAttendanceLog::STATUS_EXCUSED])->count();
        $emailed = $logs->whereNotNull('guardian_notified_at')->count();

        $teachers = User::where('role', 'teacher')
            ->whereNotNull('assigned_subject')
            ->orderBy('name')
            ->get();

        return view('admin.subject-attendance.index', compact(
            'date', 'logs', 'present', 'late', 'absent', 'emailed', 'teachers'
        ));
    }
}
