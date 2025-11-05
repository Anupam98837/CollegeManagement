<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\WelcomeMail;

class MailController extends Controller
{
    /**
     * Send a welcome email to the given address.
     */
    public function sendMail(Request $request)
    {
        $to  = "chakrabortyanupam243@gmail.com";
        $msg = "Welcome to College Management Portal";
        $sub = "New Mail";

        Mail::to($to)
            ->send(new WelcomeMail($msg, $sub));

        echo ('Email sent successfully!');
    }

}
