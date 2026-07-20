<?php

namespace App\Console\Commands;

use App\Jobs\SendGuardianAbsenceEmail;
use App\Models\AttendanceLog;
use App\Models\Student;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class MarkAbsentStudents extends Command
{
    protected $signature = 'attendance:mark-absent';

    protected $description = 'Mark any student with no fingerprint scan today as absent and email their guardian.';

    public function handle(): int
    {
        $today = Carbon::today();

        $studentIdsWithLog = AttendanceLog::forDate($today)->pluck('student_id');

        $studentsToMarkAbsent = Student::whereNotIn('id', $studentIdsWithLog)->get();

        $marked = 0;

        foreach ($studentsToMarkAbsent as $student) {
            $log = AttendanceLog::create([
                'student_id' => $student->id,
                'date' => $today->toDateString(),
                'status' => AttendanceLog::STATUS_ABSENT,
                'source' => AttendanceLog::SOURCE_MANUAL,
                'note' => 'No scan recorded — marked absent automatically at end of day.',
            ]);

            if ($student->guardian_email) {
                SendGuardianAbsenceEmail::dispatch($log->id);
            }

            $marked++;
        }

        $this->info("Marked {$marked} student(s) absent for {$today->toDateString()}.");

        return self::SUCCESS;
    }
}
