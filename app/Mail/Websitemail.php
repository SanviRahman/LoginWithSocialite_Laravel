<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class Websitemail extends Mailable
{
    use Queueable, SerializesModels;

    public $subject;
    public $body;
    public $link;

    /**
     * Create a new message instance.
     */
    public function __construct($subject, $body, $link = null)
    {
        $this->subject = $subject;
        $this->body    = $body;
        $this->link    = $link;
    }

    public function build()
    {
        return $this->subject($this->subject)
            ->view('email')
            ->with([
                'subject' => $this->subject,
                'body'    => $this->body,
                'link'    => $this->link,
            ]);
    }

}