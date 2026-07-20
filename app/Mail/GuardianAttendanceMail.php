<?php

namespace App\Mail;

use App\Models\AttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuardianAttendanceMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  'in'|'out'  $event
     */
    public function __construct(public AttendanceLog $log, public string $event)
    {
    }

    public function envelope(): Envelope
    {
        $studentName = $this->log->student->fullName();

        $subject = $this->event === 'out'
            ? "[BANTAY] {$studentName} checked out"
            : "[BANTAY] {$studentName} arrived at school";

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.attendance-scan');
    }
}
