<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMailController extends Controller
{
    public function index(Request $request)
    {
    	$offices = $request->all();

        foreach($offices['selected_offices'] as $email){
            Mail::send([], $email, function($message) use ($email) {
                $message->to($email['contact_email']);
                $message->subject($email['title']);
                $message->setBody($email['body']);
            });
        }
    }
}
