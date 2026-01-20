<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\Analytics;

class AnalyticsController extends Controller
{
    public function index()
    {
        return view('pages.analytics');
    }
}
