<?php

namespace App\Http\Controllers\Admin\Ajustes;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SmtpTestController extends Controller
{
    public function index()
    {
        return view("admin.ajustes.smtp-test");
    }
}
