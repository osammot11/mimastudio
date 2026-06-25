<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactRequestController extends Controller
{
    public function index(): View
    {
        $contactRequests = ContactRequest::query()
            ->latest()
            ->paginate(25);

        return view('admin.contact-requests.index', compact('contactRequests'));
    }

    public function show(ContactRequest $contactRequest): View
    {
        if (! $contactRequest->viewed_at) {
            $contactRequest->update(['viewed_at' => now()]);
        }

        return view('admin.contact-requests.show', compact('contactRequest'));
    }

    public function destroy(ContactRequest $contactRequest): RedirectResponse
    {
        $contactRequest->delete();

        return redirect()
            ->route('admin.contact-requests.index')
            ->with('status', 'Richiesta eliminata.');
    }
}
