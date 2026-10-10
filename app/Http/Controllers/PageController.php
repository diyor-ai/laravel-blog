<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class PageController extends Controller
{
    public function main(): View
    {
        return view('main');
    }

    public function about(): View
    {
        return view('about');
    }

    public function services(): View
    {
        return view('services');
    }

    public function projects(): View
    {
        return view('project');
    }

    public function contact(): View
    {
        return view('contact');
    }

    public function signal(): View
    {
        return view('signal');
    }
}
