<?php

namespace App\Http\Controllers;

use App\Mail\ClientPortalAccess;
use App\Models\Client;
use App\Models\WorkDelivery;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;

class ClientPortalController extends Controller
{
    public function login(Request $request): View|RedirectResponse
    {
        if ($request->session()->has('client_portal_email')) {
            return redirect()->route('client-area.index');
        }

        return view('client-area.login');
    }

    public function sendAccessLink(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        $email = $this->normalizeEmail($validated['email']);
        $hasPortalContent = $this->hasPortalContent($email);

        if ($hasPortalContent) {
            $url = URL::temporarySignedRoute(
                'client-area.authenticate',
                now()->addMinutes(30),
                ['token' => Crypt::encryptString($email)],
            );

            Mail::to($email)->send(new ClientPortalAccess($url));
        }

        return back()->with(
            'status',
            'Se questa email è associata a un lavoro, riceverai a breve il link di accesso.',
        );
    }

    public function authenticate(Request $request): RedirectResponse
    {
        try {
            $email = $this->normalizeEmail(
                Crypt::decryptString((string) $request->query('token')),
            );
        } catch (DecryptException) {
            abort(403, 'Link di accesso non valido.');
        }

        abort_unless(
            $this->hasPortalContent($email),
            403,
            'Non risultano lavori associati a questa email.',
        );

        $request->session()->regenerate();
        $request->session()->put('client_portal_email', $email);

        $workDeliveryId = $request->integer('work_delivery');

        if ($workDeliveryId && $this->deliveryForEmail($workDeliveryId, $email)) {
            return redirect()->route('client-area.show', $workDeliveryId);
        }

        $clientId = $request->integer('client');
        $client = $clientId ? $this->clientForEmail($clientId, $email) : null;

        if ($client) {
            return redirect()->route('client-area.clients.show', $client);
        }

        return redirect()
            ->route('client-area.index')
            ->with('status', 'Accesso effettuato.');
    }

    public function index(Request $request): View
    {
        $email = (string) $request->session()->get('client_portal_email');
        $clients = Client::query()
            ->portalVisible()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->orderBy('sort_order')
            ->orderByDesc('client_date')
            ->get();
        $workDeliveries = WorkDelivery::query()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->orderByDesc('work_date')
            ->orderByDesc('id')
            ->get();

        return view('client-area.index', compact('clients', 'email', 'workDeliveries'));
    }

    public function showClient(Request $request, Client $client): View
    {
        $email = (string) $request->session()->get('client_portal_email');

        abort_unless(
            $client->is_portal_visible
                && $this->normalizeEmail((string) $client->email) === $email,
            404,
        );

        $client->load('images');

        return view('client-area.client', compact('client'));
    }

    public function show(Request $request, WorkDelivery $workDelivery): View
    {
        $email = (string) $request->session()->get('client_portal_email');

        abort_unless(
            $this->normalizeEmail($workDelivery->email) === $email,
            404,
        );

        return view('client-area.show', compact('workDelivery'));
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget('client_portal_email');
        $request->session()->regenerateToken();

        return redirect()
            ->route('client-area.login')
            ->with('status', 'Hai effettuato il logout.');
    }

    private function deliveryForEmail(int $id, string $email): bool
    {
        return WorkDelivery::query()
            ->whereKey($id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists();
    }

    private function hasPortalContent(string $email): bool
    {
        return Client::query()
            ->portalVisible()
            ->whereRaw('LOWER(email) = ?', [$email])
            ->exists()
            || WorkDelivery::query()
                ->whereRaw('LOWER(email) = ?', [$email])
                ->exists();
    }

    private function clientForEmail(int $id, string $email): ?Client
    {
        return Client::query()
            ->portalVisible()
            ->whereKey($id)
            ->whereRaw('LOWER(email) = ?', [$email])
            ->first();
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }
}
