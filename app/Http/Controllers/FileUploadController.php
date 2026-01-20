<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\FileUpload;

class FileUploadController extends Controller
{
    public function index()
    {
        return view('pages.file-upload');
    }
}
