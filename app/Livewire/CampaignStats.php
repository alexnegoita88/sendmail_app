<?php

namespace App\Livewire;

use App\Models\Campaign;
use App\Models\EmailRecipient;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Layout;

class CampaignStats extends Component
{
    use WithPagination;

    public $campaignId;
    public $search = '';
    public $statusFilter = '';

    public function mount($id)
    {
        $this->campaignId = $id;
    }

    #[Layout('layouts.app')]
    public function render()
    {
        $campaign = Campaign::with(['emailTemplate', 'emailList'])->findOrFail($this->campaignId);

        $recipientsQuery = EmailRecipient::query()
            ->where('campaign_id', '=', (int) $this->campaignId)
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('email', 'like', '%' . $this->search . '%')
                        ->orWhere('name', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', '=', $this->statusFilter);
            })
            ->orderBy('id', 'desc');

        $recipients = $recipientsQuery->paginate(15);

        // Stats for cards
        $stats = [
            'total' => EmailRecipient::query()->where('campaign_id', '=', $this->campaignId)->count(),
            'sent' => EmailRecipient::query()->where('campaign_id', '=', $this->campaignId)->where('status', '=', 'sent')->count(),
            'opened' => EmailRecipient::query()->where('campaign_id', '=', $this->campaignId)->whereNotNull('opened_at')->count(),
            'failed' => EmailRecipient::query()->where('campaign_id', '=', $this->campaignId)->where('status', '=', 'failed')->count(),
            'pending' => EmailRecipient::query()->where('campaign_id', '=', $this->campaignId)->where('status', '=', 'pending')->count(),
        ];

        return view('livewire.campaign-stats', [
            'campaign' => $campaign,
            'recipients' => $recipients,
            'stats' => $stats,
        ]);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }
}
