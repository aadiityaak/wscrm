<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class DomainPriceController extends Controller
{
    public function index(): Response
    {
        $domainPrices = DomainPrice::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('extension', 'like', "%{$search}%");
            })
            ->orderBy('extension')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/DomainPrices/Index', [
            'domainPrices' => $domainPrices,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/DomainPrices/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'extension' => 'required|string|unique:domain_prices,extension',
            'register_price' => 'required|numeric|min:0',
            'renew_price' => 'required|numeric|min:0',
            'transfer_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        DomainPrice::create($validated);

        return redirect()->route('admin.domain-prices.index')
            ->with('success', 'Domain price created successfully.');
    }

    public function show(DomainPrice $domainPrice): Response
    {
        return Inertia::render('Admin/DomainPrices/Show', [
            'domainPrice' => $domainPrice,
        ]);
    }

    public function edit(DomainPrice $domainPrice): Response
    {
        return Inertia::render('Admin/DomainPrices/Edit', [
            'domainPrice' => $domainPrice,
        ]);
    }

    public function update(Request $request, DomainPrice $domainPrice): RedirectResponse
    {
        $validated = $request->validate([
            'extension' => 'required|string|unique:domain_prices,extension,' . $domainPrice->id,
            'register_price' => 'required|numeric|min:0',
            'renew_price' => 'required|numeric|min:0',
            'transfer_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        $domainPrice->update($validated);

        return redirect()->route('admin.domain-prices.index')
            ->with('success', 'Domain price updated successfully.');
    }

    public function destroy(DomainPrice $domainPrice): RedirectResponse
    {
        $domainPrice->delete();

        return redirect()->route('admin.domain-prices.index')
            ->with('success', 'Domain price deleted successfully.');
    }
}