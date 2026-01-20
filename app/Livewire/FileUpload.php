<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Services\FileUploadService;
use App\Models\EmailList;

class FileUpload extends Component
{
    use WithFileUploads;

    public $file;
    public $name;
    public $uploading = false;
    public $uploadProgress = 0;

    protected $rules = [
        'file' => 'required|file|mimes:xlsx,csv,json|max:10240',
        'name' => 'required|string|max:255',
    ];

    public function render()
    {
        $emailLists = EmailList::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.file-upload', [
            'emailLists' => $emailLists
        ]);
    }

    public function uploadFile()
    {
        $this->validate();

        $this->uploading = true;
        $this->uploadProgress = 0;

        try {
            $service = new FileUploadService();
            $result = $service->processFile($this->file, $this->name, auth()->id());

            if ($result['success']) {
        session()->flash('message', 'Fișier încărcat și procesat cu succes!');
        $this->reset(['file', 'name']);
        $this->uploadProgress = 100;
    } else {
        session()->flash('error', $result['errors']);
    }
} catch (\Exception $e) {
    session()->flash('error', 'Eroare la procesarea fișierului: ' . $e->getMessage());
}

$this->uploading = false;
    }

    public function deleteList($listId)
    {
        $list = EmailList::where('id', $listId)
            ->where('user_id', auth()->id())
            ->first();

        if ($list) {
            // Delete associated file
            if ($list->file_path) {
                \Storage::delete('public/' . $list->file_path);
            }

            // Delete associated recipients
            $list->emailRecipients()->delete();

            // Delete the list
            $list->delete();

            session()->flash('message', 'Lista de emailuri ștearsă cu succes!');
        }
    }
}
