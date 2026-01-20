<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\EmailTemplates;

class EmailTemplateController extends Controller
{
    public function index()
    {
        return view('pages.email-templates');
    }
}
