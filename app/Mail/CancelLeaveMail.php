<?php

namespace App\Mail;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CancelLeaveMail extends Mailable
{
    use Queueable, SerializesModels;
    public User $requester;
    public Model $leaveRequest;

    /**
     * Create a new message instance.
     ** @param Model $leaveRequest
     * @return void
     */
    public function __construct($requester, Model $leaveRequest)
    {
        $this->requester = $requester;
        $this->leaveRequest = $leaveRequest;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->requester->name .' has cancel their '.$this->leaveRequest->leaveBalance->leave->name,
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'mail.cancel-leave-mail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
