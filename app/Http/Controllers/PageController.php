<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    public function home(): View
    {
        return view('home');
    }

    public function terms(): View
    {
        return view('terms-of-use');
    }

    public function privacy(): View
    {
        return view('privacy-policy');
    }
}
