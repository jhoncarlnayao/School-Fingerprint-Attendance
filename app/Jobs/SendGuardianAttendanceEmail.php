<?php

namespace App\Jobs;

use App\Mail\GuardianAttendanceMail;
use App\Models\AttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class SendGuardianAttendanceEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    /**
     * @param  'in'|'out'  $event
     */
    public function __construct(public int $attendanceLogId, public string $event)
    {
    }

    public function handle(): void
    {
        $log = AttendanceLog::with('student')->find($this->attendanceLogId);

        if (! $log || ! $log->student || empty($log->student->guardian_email)) {
            return;
        }

        try {
            Mail::to($log->student->guardian_email)->send(new GuardianAttendanceMail($log, $this->event));

            $log->update([
                $this->event === 'out' ? 'guardian_notified_out_at' : 'guardian_notified_in_at' => now(),
                'guardian_notify_error' => null,
            ]);
        } catch (Throwable $e) {
            // Keep scanning working even if SMTP isn't configured — just
            // record the failure so it's visible in the admin attendance view.
            $log->update(['guardian_notify_error' => $e->getMessage()]);

            throw $e;
        }
    }
}
