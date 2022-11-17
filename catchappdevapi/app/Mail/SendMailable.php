<?php

namespace catchapp\Mail;

use catchapp\Models\EmailConfiguration;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendMailable extends Mailable
{
    use Queueable, SerializesModels;
    public $mail;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(EmailConfiguration $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = $this->mail->mail_subject;
        $from = $this->mail->mail_from;
        return $this->subject($subject)->from($from)
            ->view('backend.emails.email-template');
    }
}
