<?php
namespace App\Http\Controllers\Teacher;

use App\Http\Controllers\Controller;
use App\Models\AttendanceLog;
use App\Models\Announcement;
use App\Models\Student;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ManagementController extends Controller
{
    public function attendance() {
        $t=auth()->user();
        $students=Student::where('grade_level',$t->assigned_grade_level)->where('section',$t->assigned_section)->get();
        return view('teacher.attendance',compact('students'));
    }

    public function mark(Request $r, Student $student){
        AttendanceLog::updateOrCreate(
          ['student_id'=>$student->id,'date'=>now()->toDateString()],
          ['status'=>$r->status,'source'=>'manual','marked_by'=>auth()->id()]
        );
        return back()->with('status','Attendance updated');
    }

    public function export(): StreamedResponse {
      $rows=AttendanceLog::latest()->get();
      $headers=['Content-Type'=>'text/csv'];
      return response()->streamDownload(function() use ($rows){
        $f=fopen('php://output','w');
        fputcsv($f,['Student','Date','Status']);
        foreach($rows as $r){ fputcsv($f,[$r->student?->fullName(),$r->date,$r->status]);}
        fclose($f);
      },'attendance.csv',$headers);
    }

    public function announcements() { $items=Announcement::latest()->get(); return view('teacher.announcements',compact('items')); }
}
