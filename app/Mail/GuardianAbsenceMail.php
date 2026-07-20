<?php

namespace App\Mail;

use App\Models\AttendanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GuardianAbsenceMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public AttendanceLog $log)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[BANTAY] '.$this->log->student->fullName().' was marked absent today',
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.attendance-absent');
    }
}
