<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Campaign;
use App\Models\EmailTemplate;
use App\Models\EmailList;
use App\Jobs\ProcessCampaignJob;

class Campaigns extends Component
{
    public $name;
    public $emailTemplateId;
    public $emailListId;
    public $status = 'pending';

    protected $rules = [
        'name' => 'required|string|max:255',
        'emailTemplateId' => 'required|exists:email_templates,id',
        'emailListId' => 'required|exists:email_lists,id',
    ];

    public function render()
    {
        $campaigns = Campaign::where('user_id', auth()->id())
            ->with(['emailTemplate', 'emailList'])
            ->latest()
            ->paginate(10);

        $templates = EmailTemplate::where('user_id', auth()->id())->get();
        $emailLists = EmailList::where('user_id', auth()->id())
            ->where('status', 'completed')
            ->get();

        return view('livewire.campaigns', [
            'campaigns' => $campaigns,
            'templates' => $templates,
            'emailLists' => $emailLists
        ]);
    }

    public function createCampaign()
    {
        $this->validate();

        // Check if email list has valid emails
        $emailList = EmailList::find($this->emailListId);
        if ($emailList->valid_emails === 0) {
            session()->flash('error', 'Lista de emailuri nu conține adrese valide!');
            return;
        }

        $campaign = Campaign::create([
            'name' => $this->name,
            'email_template_id' => $this->emailTemplateId,
            'email_list_id' => $this->emailListId,
            'status' => 'pending',
            'user_id' => auth()->id(),
        ]);

        session()->flash('message', 'Campanie creată cu succes!');

        // Reset form
        $this->reset(['name', 'emailTemplateId', 'emailListId']);
    }

    public function startCampaign($campaignId)
    {
        $campaign = Campaign::where('id', $campaignId)
            ->where('user_id', auth()->id())
            ->first();

        if ($campaign && $campaign->status === 'pending') {
            // Dispatch campaign processing job
            ProcessCampaignJob::dispatch($campaign->id);

            $campaign->update(['status' => 'running']);

            session()->flash('message', 'Campanie pornită cu succes!');
        }
    }

    public function pauseCampaign($campaignId)
    {
        $campaign = Campaign::where('id', $campaignId)
            ->where('user_id', auth()->id())
            ->first();

        if ($campaign && $campaign->status === 'running') {
            $campaign->update(['status' => 'paused']);
            session()->flash('message', 'Campanie oprită temporar!');
        }
    }

    public function deleteCampaign($campaignId)
    {
        $campaign = Campaign::where('id', $campaignId)
            ->where('user_id', auth()->id())
            ->first();

        if ($campaign) {
            $campaign->delete();
            session()->flash('message', 'Campanie ștearsă cu succes!');
        }

        $this->dispatch('campaignDeleted');
    }

    public function getStatusColor($status)
    {
        switch ($status) {
            case 'pending': return 'bg-gray-100 text-gray-800';
            case 'running': return 'bg-green-100 text-green-800';
            case 'completed': return 'bg-blue-100 text-blue-800';
            case 'failed': return 'bg-red-100 text-red-800';
            case 'paused': return 'bg-yellow-100 text-yellow-800';
            default: return 'bg-gray-100 text-gray-800';
        }
    }
}
