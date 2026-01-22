<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\EmailTemplate;

class EmailTemplates extends Component
{
    public $name;
    public $subject;
    public $content;
    public $isHtml = true;
    public $mjmlContent = '';
    public $editorData = [];
    public $editingTemplateId = null;
    public $useVisualEditor = false;

    protected $rules = [
        'name' => 'required|string|max:255',
        'subject' => 'required|string|max:255',
        'content' => 'required|string',
        'isHtml' => 'boolean',
    ];

    protected $listeners = [
        'mjmlContentUpdated' => 'updateMjmlContent',
        'editorDataUpdated' => 'updateEditorData',
    ];

    public function render()
    {
        $templates = EmailTemplate::where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('livewire.email-templates', [
            'templates' => $templates
        ]);
    }

    public function saveTemplate()
    {
        $this->validate();

        // If using visual editor, convert MJML to HTML for content field
        if ($this->useVisualEditor && $this->mjmlContent) {
            // For now, we'll store MJML in content field, but ideally we'd convert to HTML
            $this->content = $this->mjmlContent;
        }

        if ($this->editingTemplateId) {
            $template = EmailTemplate::where('id', $this->editingTemplateId)
                ->where('user_id', auth()->id())
                ->first();

            if ($template) {
                $updateData = [
                    'name' => $this->name,
                    'subject' => $this->subject,
                    'content' => $this->content,
                    'is_html' => $this->isHtml,
                ];

                if ($this->useVisualEditor) {
                    $updateData['mjml_content'] = $this->mjmlContent;
                    $updateData['editor_data'] = $this->editorData;
                }

                $template->update($updateData);
                session()->flash('message', 'Șablon actualizat cu succes!');
            }
        } else {
            $createData = [
                'name' => $this->name,
                'subject' => $this->subject,
                'content' => $this->content,
                'is_html' => $this->isHtml,
                'user_id' => auth()->id(),
            ];

            if ($this->useVisualEditor) {
                $createData['mjml_content'] = $this->mjmlContent;
                $createData['editor_data'] = $this->editorData;
            }

            EmailTemplate::create($createData);
            session()->flash('message', 'Șablon creat cu succes!');
        }

        $this->resetForm();
    }

    public function editTemplate($templateId)
    {
        $template = EmailTemplate::where('id', $templateId)
            ->where('user_id', auth()->id())
            ->first();

        if ($template) {
            $this->editingTemplateId = $template->id;
            $this->name = $template->name;
            $this->subject = $template->subject;
            $this->content = $template->content;
            $this->isHtml = $template->is_html;
            $this->mjmlContent = $template->mjml_content ?? '';
            $this->editorData = $template->editor_data ?? [];

            // Check if template was created with visual editor
            $this->useVisualEditor = !empty($template->mjml_content);
        }
    }

    public function deleteTemplate($templateId)
    {
        $template = EmailTemplate::where('id', $templateId)
            ->where('user_id', auth()->id())
            ->first();

        if ($template) {
            $template->delete();
            session()->flash('message', 'Șablon șters cu succes!');
        }
    }

    public function resetForm()
    {
        $this->reset(['name', 'subject', 'content', 'isHtml', 'mjmlContent', 'editorData', 'editingTemplateId', 'useVisualEditor']);
    }

    public function toggleVisualEditor()
    {
        $this->useVisualEditor = !$this->useVisualEditor;

        // Reset MJML content when switching modes
        if (!$this->useVisualEditor) {
            $this->mjmlContent = '';
            $this->editorData = [];
        }

        // Dispatch event to initialize editor when switching to visual mode
        if ($this->useVisualEditor) {
            $this->dispatch('init-grapesjs-editor');
        }
    }

    public function updateMjmlContent($content)
    {
        $this->mjmlContent = $content;
    }

    public function updateEditorData($data)
    {
        $this->editorData = $data;
    }

    public function previewTemplate()
    {
        return $this->content;
    }
}
