<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Jobs\SendEmail;
class SendMailController extends Controller
{
    public function index(Request $request)
    {
        $this->dispatch(new SendEmail($request->all()));
    }
}
