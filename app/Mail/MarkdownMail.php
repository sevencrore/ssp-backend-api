<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MarkdownMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(string $subject, array $data = [])
    {
        $this->data = $data;
        $this->subject = $subject;
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        $mail = $this->subject($this->subject)
            ->html($this->getHtmlContent()); // Using inline HTML instead of Blade

        // Attach file if it exists
        if (!empty($this->data['attachments'])) {
            $mail->attach($this->data['attachments']);
        }

        return $mail;
    }

    /**
     * Generate the email's inline HTML content.
     */
    private function getHtmlContent(): string
    {
        return "
        <html>
        <head>
            <title>{$this->subject}</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 20px; }
                h1 { color: #333; }
                p { font-size: 16px; }
                .footer { margin-top: 20px; font-size: 14px; color: #777; }
            </style>
        </head>
        <body>
            <h1>Hello, {$this->data['to']}</h1>
            <p>{$this->data['message']}</p>
            <p class='footer'>Thanks,<br>" . config('app.name') . "</p>
        </body>
        </html>";
    }
}
