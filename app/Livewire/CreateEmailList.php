<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmailList;
use Livewire\Attributes\Layout;

class CreateEmailList extends Component
{
    public $name;

    protected $rules = [
        'name' => 'required|string|max:255',
    ];

    public function createList()
    {
        $this->validate();

        $list = EmailList::create([
            'name' => $this->name,
            'user_id' => auth()->id(),
            'status' => 'completed', // Manual lists are ready immediately
            'file_type' => 'manual',
            'total_emails' => 0,
            'valid_emails' => 0,
        ]);

        session()->flash('message', 'Lista a fost creată manual. Acum poți adăuga adrese de email.');

        return redirect()->route('email-lists.edit', $list->id);
    }

    #[Layout('layouts.app')]
    public function render()
    {
        return view('livewire.create-email-list');
    }
}
