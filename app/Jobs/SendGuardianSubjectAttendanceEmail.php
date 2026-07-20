<?php

namespace App\Jobs;

use App\Mail\GuardianSubjectAttendanceMail;
use App\Models\SubjectAttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendGuardianSubjectAttendanceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(public int $subjectAttendanceLogId)
    {
    }

    public function handle(): void
    {
        $log = SubjectAttendanceLog::with(['student', 'teacher'])->find($this->subjectAttendanceLogId);

        if (! $log || ! $log->student || empty($log->student->guardian_email)) {
            return;
        }

        try {
            Mail::to($log->student->guardian_email)->send(new GuardianSubjectAttendanceMail($log));

            $log->update([
                'guardian_notified_at' => now(),
                'guardian_notify_error' => null,
            ]);
        } catch (Throwable $e) {
            $log->update(['guardian_notify_error' => $e->getMessage()]);

            throw $e;
        }
    }
}
