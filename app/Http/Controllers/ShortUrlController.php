<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShortUrlController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isSuperAdmin()) {
            $urls = ShortUrl::with(['user', 'company'])->latest()->get();
        } elseif ($user->isAdmin()) {
            $urls = ShortUrl::with('user')
                ->where('company_id', $user->company_id)
                ->latest()
                ->get();
        } else {
            $urls = ShortUrl::with('user')->where('user_id', $user->id)->latest()->get();
        }

        return view('short-urls.index', compact('urls'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'original_url' => 'required|url|max:2000',
        ]);

        do {
            $code = Str::random(6);
        } while (ShortUrl::where('short_code', $code)->exists());

        ShortUrl::create([
            'user_id' => $request->user()->id,
            'company_id' => $request->user()->company_id,
            'original_url' => $data['original_url'],
            'short_code' => $code,
        ]);

        return redirect()->route('short-urls.index');
    }

    public function redirect($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->firstOrFail();

        return redirect()->away($shortUrl->original_url);
    }
}