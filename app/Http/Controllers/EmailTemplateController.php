<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\EmailTemplate;

class EmailTemplateController extends Controller
{
    public function index()
    {
        $templates = EmailTemplate::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('pages.email-templates', compact('templates'));
    }

    public function create()
    {
        return view('pages.email-templates-create');
    }
}
