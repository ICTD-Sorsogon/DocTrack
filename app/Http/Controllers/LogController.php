<?php

namespace App\Http\Controllers;

use App\Models\Log;
class LogController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }

    public function index()
    {
        return  Log::latest()->with('user')->get();
    }
}
