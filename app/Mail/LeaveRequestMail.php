<?php

namespace App\Mail;


use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LeaveRequestMail extends Mailable
{
    use Queueable, SerializesModels;
    public User $requester;
    public LeaveRequest $leaveRequest;
    public String $type;
    public String $flag;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($requester, $leaveRequest, $type, $flag)
    {
        $this->requester = $requester;
        $this->leaveRequest = $leaveRequest;
        $this->type = $type;
        $this->flag = $flag;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        if ($this->flag === 'RequestLeave') {
            return new Envelope(
                subject: $this->requester->name . ' ' . $this->type .' Request',
            );
        }

        return new Envelope(
            subject: $this->type .' Request Status Update',
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
            markdown: 'mail.leave-request-mail',
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
