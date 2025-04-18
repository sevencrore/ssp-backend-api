<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Repositories\MailRepository;
use Illuminate\Support\Facades\Mail;
use App\Models\Mail as MailModel;
use App\Http\Controllers\API\BaseController as BaseController;
use Exception;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use App\Mail\MarkdownMail;

class EMailController extends BaseController
{
    //  /** @var MailRepository */
    //  private $mailRepository;

    public function sendEmailAPI(Request $request)
    {
        try {
            // Validate the input
            $request->validate([
                'to' => 'required|email',
                'subject' => 'required|string',
                'message' => 'required|string',
                'attachments' => 'nullable|file|max:2048', // Optional file attachment
            ]);

            $input = $request->only(['to', 'subject', 'message']);
            
            $input['user_id'] = $request->user_id;
            $mail = MailModel::create($input);

            // Handle file upload (if exists)
            if ($request->hasFile('attachments')) {
                try {
                    $attachmentPath = $request->file('attachments')->store('email_attachments');
                    $input['attachments'] = storage_path("app/" . $attachmentPath);
                } catch (FileException $e) { 
                    return response()->json(['error' => 'File upload failed: ' . $e->getMessage()], 500);
                }
            } else {
                $input['attachments'] = null;
            }

            // Attempt to send the email
            try {
                Mail::to($input['to'])->send(new MarkdownMail($input['subject'],$input));
            }  catch (Exception $e) {
                return response()->json(['error' => 'Email sending failed', 'details' => $e->getMessage()], 500);
            }

            return response()->json(['message' => 'Email sent successfully!'], 200);

        } catch (ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'details' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'Unexpected error occurred', 'details' => $e->getMessage()], 500);
        }
    }

    public function sendOtp($to_emailId,$otp,$subject){
        $data=[
           'otp' =>$otp,
        ];
        try {
            Mail::to($to_emailId)->send(new MarkdownMail($subject,$data));
            return [
                'success' => true,
                'message' => 'Email OTP sent succesfully',
                ];
        }  catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Email OTP sending failed',
                'error' => 'Email sending failed', ];
        }
    }
}
