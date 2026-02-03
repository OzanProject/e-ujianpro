<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show($slug)
    {
        $validPages = ['privacy', 'terms', 'contact'];

        if (!in_array($slug, $validPages)) {
            abort(404);
        }

        $titles = [
            'privacy' => 'Kebijakan Privasi',
            'terms' => 'Syarat & Ketentuan',
            'contact' => 'Hubungi Kami',
        ];

        $contentKey = 'content_' . $slug;
        $content = Setting::getValue($contentKey, '<p>Belum ada konten.</p>');
        $title = $titles[$slug];

        return view('pages.show', compact('title', 'content'));
    }
}
