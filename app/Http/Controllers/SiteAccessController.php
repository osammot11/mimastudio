<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteAccessController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        if ($request->session()->get('site_access_granted') === true) {
            return redirect()->route('home');
        }

        return view('site-access');
    }

    public function authenticate(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        $expectedCode = (string) config('site.access_code');

        if ($expectedCode === '' || ! hash_equals($expectedCode, $validated['code'])) {
            return back()
                ->withErrors(['code' => 'Il codice inserito non è corretto.']);
        }

        $request->session()->regenerate();
        $request->session()->put('site_access_granted', true);

        return redirect()->intended(route('home'));
    }
}
