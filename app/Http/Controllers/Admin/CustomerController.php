<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\WorkDelivery;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(): View
    {
        $customers = Customer::query()
            ->withCount('works')
            ->orderBy('name')
            ->get();

        return view('admin.customers.index', compact('customers'));
    }

    public function create(): View
    {
        return view('admin.customers.create', [
            'customer' => new Customer,
        ]);
    }

    public function accessLinks(): View
    {
        $expiresAt = now()->addDays(7);
        $deliveryCounts = WorkDelivery::query()
            ->get(['email'])
            ->countBy(fn (WorkDelivery $delivery): string => $this->normalizeEmail($delivery->email));
        $customers = Customer::query()
            ->withCount([
                'works as portal_works_count' => fn ($query) => $query->portalVisible(),
            ])
            ->orderBy('name')
            ->get()
            ->each(function (Customer $customer) use ($deliveryCounts, $expiresAt): void {
                $email = $this->normalizeEmail((string) $customer->email);
                $deliveryCount = $deliveryCounts->get($email, 0);
                $contentCount = $customer->portal_works_count + $deliveryCount;

                $customer->setAttribute('portal_content_count', $contentCount);
                $customer->setAttribute(
                    'portal_access_url',
                    $email && $contentCount > 0
                        ? URL::temporarySignedRoute(
                            'client-area.authenticate',
                            $expiresAt,
                            ['token' => Crypt::encryptString($email)],
                        )
                        : null,
                );
            });

        return view('admin.customers.access-links', compact('customers', 'expiresAt'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);

        if ($request->hasFile('logo')) {
            $data['logo_path'] = $request->file('logo')->store('customer-logos', 'public');
        }

        $customer = Customer::create($data);

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('status', 'Cliente creato.');
    }

    public function edit(Customer $customer): View
    {
        $customer->load(['works' => fn ($query) => $query->orderByDesc('client_date')]);

        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $data = $this->validatedData($request, $customer);

        if ($request->hasFile('logo')) {
            $this->deleteLogo($customer->logo_path);
            $data['logo_path'] = $request->file('logo')->store('customer-logos', 'public');
        } elseif ($request->boolean('remove_logo')) {
            $this->deleteLogo($customer->logo_path);
            $data['logo_path'] = null;
        }

        $customer->update($data);

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('status', 'Cliente aggiornato.');
    }

    private function validatedData(Request $request, ?Customer $customer = null): array
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customer),
            ],
            'logo' => ['nullable', 'image', 'mimes:png', 'max:4096'],
            'remove_logo' => ['nullable', 'boolean'],
        ]);

        unset($validated['logo'], $validated['remove_logo']);

        return $validated;
    }

    private function normalizeEmail(string $email): string
    {
        return strtolower(trim($email));
    }

    private function deleteLogo(?string $path): void
    {
        if ($path) {
            Storage::disk('public')->delete($path);
        }
    }
}
