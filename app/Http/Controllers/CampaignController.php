<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Livewire\Campaigns;

class CampaignController extends Controller
{
    public function index()
    {
        return view('pages.campaigns');
    }
}
