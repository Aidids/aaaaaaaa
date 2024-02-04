<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EFormCancelMail extends Mailable
{
    use Queueable, SerializesModels;

    public User $requester;
    public Model $eform;
    public String $mailSubject;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($requester, $eform, $mailSubject)
    {
        $this->requester = $requester;
        $this->eform = $eform;
        $this->mailSubject = $mailSubject;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        $text = explode('\\', $this->mailSubject)[2];
        $mail_subject = preg_replace('/(?<!\ )[A-Z]/', ' $0', $text);

        return new Envelope(
            subject: $mail_subject . ' E-Form Cancellation',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        $text = explode('\\', $this->mailSubject)[2];
        $title = strtolower(preg_replace('/(?<=\w)([A-Z])/', '-$1', $text));

        $bladeFileName = $title.'-cancel-mail';

        return new Content(
            markdown: 'mail.' . $bladeFileName,
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
