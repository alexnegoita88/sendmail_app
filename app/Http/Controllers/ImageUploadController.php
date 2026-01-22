<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ImageUploadController extends Controller
{
    public function upload(Request $request)
    {
        // Validare: fișier obligatoriu, imagine, max 512 KB
        $request->validate([
            'file' => 'required|image|max:512', // KB
        ]);

        $file = $request->file('file');

        // Generare nume unic
        $filename = time() . '-' . Str::random(12) . '.' . $file->getClientOriginalExtension();

        // Salvare în public/uploads
        $destinationPath = public_path('uploads');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true);
        }
        $file->move($destinationPath, $filename);

        // URL public
        $url = asset('uploads/' . $filename);

        return response()->json(['url' => $url]);
    }
}
