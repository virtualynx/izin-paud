<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class PermitController extends Controller
{
    public function __construct()
    {
    }

    public function index(){
        return view('pages.home');
        // return redirect('docs');
    }

    public function request_page(){
        return view('pages.permit.request');
    }
}
