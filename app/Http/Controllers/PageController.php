<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function inicio()
    {
        return view('inicio');
    }

    public function torneos()
    {
        return view('torneos');
    }
}
