<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Mail\WorkDeliveryReady;
use App\Models\WorkDelivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Throwable;

class WorkDeliveryController extends Controller
{
    public function index(): View
    {
        $workDeliveries = WorkDelivery::query()
            ->latest()
            ->paginate(25);

        return view('admin.work-deliveries.index', compact('workDeliveries'));
    }

    public function create(): View
    {
        return view('admin.work-deliveries.create', [
            'workDelivery' => new WorkDelivery([
                'work_date' => now()->toDateString(),
            ]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $workDelivery = WorkDelivery::create($this->validatedData($request));

        return $this->sendDelivery($workDelivery, 'Consegna salvata e inviata al cliente.');
    }

    public function show(WorkDelivery $workDelivery): View
    {
        return view('admin.work-deliveries.show', compact('workDelivery'));
    }

    public function resend(WorkDelivery $workDelivery): RedirectResponse
    {
        return $this->sendDelivery($workDelivery, 'Email di consegna inviata nuovamente.');
    }

    public function destroy(WorkDelivery $workDelivery): RedirectResponse
    {
        $workDelivery->delete();

        return redirect()
            ->route('admin.work-deliveries.index')
            ->with('status', 'Consegna eliminata.');
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'work_description' => ['required', 'string', 'max:5000'],
            'work_date' => ['required', 'date'],
            'identifier_code' => ['nullable', 'string', 'max:100', Rule::unique('work_deliveries')],
            'email' => ['required', 'email', 'max:255'],
            'gallery_url' => ['required', 'url:http,https', 'max:2048'],
        ]);

        $validated['email'] = strtolower(trim($validated['email']));

        return $validated;
    }

    private function sendDelivery(WorkDelivery $workDelivery, string $successMessage): RedirectResponse
    {
        try {
            $portalUrl = URL::temporarySignedRoute(
                'client-area.authenticate',
                now()->addDays(7),
                [
                    'token' => Crypt::encryptString(strtolower(trim($workDelivery->email))),
                    'work_delivery' => $workDelivery->getKey(),
                ],
            );

            Mail::to($workDelivery->email)->send(
                new WorkDeliveryReady($workDelivery, $portalUrl),
            );

            $workDelivery->update([
                'sent_at' => now(),
                'last_send_error' => null,
            ]);

            return redirect()
                ->route('admin.work-deliveries.show', $workDelivery)
                ->with('status', $successMessage);
        } catch (Throwable $exception) {
            report($exception);

            $workDelivery->update([
                'last_send_error' => $exception->getMessage(),
            ]);

            return redirect()
                ->route('admin.work-deliveries.show', $workDelivery)
                ->with('status_error', 'Consegna salvata, ma l’email non è stata inviata. Puoi riprovare da questa pagina.');
        }
    }
}
