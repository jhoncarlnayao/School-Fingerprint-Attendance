<?php

namespace App\Mail;

use App\Models\SubjectAttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuardianSubjectAttendanceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public SubjectAttendanceLog $log)
    {
    }

    public function envelope(): Envelope
    {
        $studentName = $this->log->student->fullName();

        return new Envelope(
            subject: "[BANTAY] {$studentName} was present in {$this->log->subject}",
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.subject-attendance');
    }
}
