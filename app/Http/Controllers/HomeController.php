<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;

class HomeController extends Controller
{
    public function __construct()
    {
    }

    public function index(){
        return view('pages.home');
        // return redirect('docs');
    }
}
