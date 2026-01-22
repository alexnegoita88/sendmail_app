<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestGrapesController extends Controller
{
    public function index()
    {
        return view('test-grapes');
    }
}
