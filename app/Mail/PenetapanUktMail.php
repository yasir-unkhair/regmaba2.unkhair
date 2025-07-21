<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PenetapanUktMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $contents = NULL;
    protected $judul = '';
    /**
     * Create a new message instance.
     */
    public function __construct($contents, $judul)
    {
        $this->contents = $contents;
        $this->judul = $judul;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->judul,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'mails.statusukt',
            with: ['peserta' => $this->contents, 'judul' => $this->judul],
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
