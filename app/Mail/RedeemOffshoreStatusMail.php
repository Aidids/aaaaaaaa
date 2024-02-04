<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RedeemOffshoreStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public String $mailSubject;
    public User $user;
    public Model $model;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($mailSubject, $user, $model)
    {
        $this->mailSubject = $mailSubject;
        $this->user = $user;
        $this->model = $model;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: $this->user->name . ' ' . $this->mailSubject . ' Status Update',
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
            markdown: 'mail.redeem-offshore-status-mail',
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
