<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmailList;
use App\Models\EmailTemplate;
use App\Models\Campaign;
use App\Models\EmailRecipient;

class Dashboard extends Component
{
    public $stats = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $userId = auth()->id();

        $this->stats = [
            'email_lists' => EmailList::where('user_id', $userId)->where('status', 'completed')->count(),
            'templates' => EmailTemplate::where('user_id', $userId)->count(),
            'campaigns' => Campaign::where('user_id', $userId)->count(),
            'emails_sent' => EmailRecipient::whereHas('campaign', function($query) use ($userId) {
                $query->where('user_id', $userId);
            })->where('status', 'sent')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
