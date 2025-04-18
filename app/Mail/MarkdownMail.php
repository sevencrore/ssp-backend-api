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

    private function getHtmlContent()
    {
        if($this->data['otp']){
            return  $this->getOtpTemplate($this->data['otp']);
        }
        else{
            return $this->getgeneralhtmlcontent();
        }
    }

    /**
     * Generate the email's inline HTML content.
     */
    private function getgeneralhtmlcontent(): string
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

    private function getOtpTemplate($otp)
    {
        return "<html>
    <head>
        <meta charset=\"UTF-8\">
        <title>Your OTP Code</title>
    </head>
    <body style=\"font-family: Arial, sans-serif; background-color: #f6f6f6; padding: 20px;\">
        <table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\">
            <tr>
                <td align=\"center\">
                    <table style=\"max-width: 600px; background-color: #ffffff; padding: 30px; border-radius: 6px;\">
                        <tr>
                            <td>
                                <h2 style=\"color: #333333;\">Hello,</h2>
                                <p style=\"font-size: 16px; color: #555555;\">
                                    Your One-Time Password (OTP) for resetting your password is:
                                </p>
                                <p style=\"font-size: 24px; font-weight: bold; color: #2c3e50; text-align: center; margin: 30px 0;\">
                                    " . $otp . "
                                </p>
                                <p style=\"font-size: 16px; color: #555555;\">
                                    Please enter this code in the app to proceed with resetting your password.
                                    This code is valid for the next <strong>10 minutes</strong>.
                                </p>
                                <p style=\"font-size: 14px; color: #999999;\">
                                    If you did not request this, please ignore this email.
                                </p>
                                <p style=\"font-size: 16px; color: #333333; margin-top: 40px;\">
                                    Thank you,<br>
                                    <strong>" . config('app.name') . "</strong>
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>";
    }
    
}

