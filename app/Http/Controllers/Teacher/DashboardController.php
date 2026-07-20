<?php

namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $teacher = auth()->user();

        $sectionStudentCount = 0;
        if ($teacher->assigned_grade_level && $teacher->assigned_section) {
            $sectionStudentCount = Student::where('grade_level', $teacher->assigned_grade_level)
                ->where('section', $teacher->assigned_section)
                ->count();
        }

        // Announcements meant for this teacher: "all" audience or "teachers" audience.
        $announcements = Announcement::whereIn('audience', ['all', 'teachers'])
            ->latest()
            ->take(6)
            ->get();

        return view('teacher.dashboard', compact('teacher', 'sectionStudentCount', 'announcements'));
    }
}
