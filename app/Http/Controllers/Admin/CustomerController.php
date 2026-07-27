<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

    public function store(Request $request): RedirectResponse
    {
        $customer = Customer::create($this->validatedData($request));

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
        $customer->update($this->validatedData($request, $customer));

        return redirect()
            ->route('admin.customers.edit', $customer)
            ->with('status', 'Cliente aggiornato.');
    }

    private function validatedData(Request $request, ?Customer $customer = null): array
    {
        $request->merge([
            'email' => strtolower(trim((string) $request->input('email'))),
        ]);

        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore($customer),
            ],
        ]);
    }
}
