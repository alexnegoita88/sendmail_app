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

        return view('email-templates.index', [
            'templates' => $templates
        ]);
    }

    public function create()
    {
        return view('email-templates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_html' => 'boolean'

        ]);

        EmailTemplate::create([
            'name' => $request->input('name'),
            'subject' => $request->input('subject'),
            'content' => $request->input('content'),
            'is_html' => $request->is_html ?? true,
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('email-templates')
            ->with('message', 'Șablon creat cu succes!');
    }

    public function edit($id)
    {
        $template = EmailTemplate::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        return view('email-templates.edit', [
            'template' => $template
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'is_html' => 'boolean'
        ]);

        $template = EmailTemplate::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $template->update([
            'name' => $request->name,
            'subject' => $request->subject,
            'content' => $request->content,
            'is_html' => $request->is_html ?? true,
        ]);

        return redirect()->route('email-templates')
            ->with('message', 'Șablon actualizat cu succes!');
    }

    public function destroy($id)
    {
        $template = EmailTemplate::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($template) {
            $template->delete();
        }

        return redirect()->route('email-templates.index')
            ->with('message', 'Șablon șters cu succes!');
    }
}
