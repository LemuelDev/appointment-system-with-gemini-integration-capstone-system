<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelAppointment extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
     public $mailmessage;

     public $mailappointment;

       public $mailpatient;
     public $maildate;
     public $mailtime;
     public $mailtreatment;
    public function __construct($message, $date, $time, $treatment, $appointment_number, $patient_number)
    {
        $this->mailmessage = $message;
        $this->maildate = $date;
        $this->mailtime = $time;
        $this->mailtreatment = $treatment;
        $this->mailappointment = $appointment_number;
        $this->mailpatient = $patient_number;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Appointment is Canceled!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shared.canceled-email',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
