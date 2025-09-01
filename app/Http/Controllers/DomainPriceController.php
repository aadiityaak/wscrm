<?php

namespace App\Http\Controllers;

use App\Models\DomainPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainPriceController extends Controller
{
    public function index(): Response
    {
        $domainPrices = DomainPrice::active()
            ->orderBy('selling_price')
            ->get();

        return Inertia::render('Domains/Index', [
            'domainPrices' => $domainPrices,
        ]);
    }

    public function search(Request $request): Response
    {
        $domain = $request->input('domain');
        $extension = $request->input('extension', '.com');

        $domainPrice = DomainPrice::byExtension($extension)->active()->first();

        return Inertia::render('Domains/Search', [
            'domain' => $domain,
            'extension' => $extension,
            'domainPrice' => $domainPrice,
            'available' => true, // TODO: Implement domain availability check
        ]);
    }

    public function publicIndex(): Response
    {
        $domainPrices = DomainPrice::where('is_active', true)
            ->when(request('search'), function ($query, $search) {
                $query->where('extension', 'like', "%{$search}%");
            })
            ->orderBy('selling_price')
            ->get();

        return Inertia::render('Public/Domains/Index', [
            'domainPrices' => $domainPrices,
            'filters' => request()->only(['search']),
        ]);
    }

    public function publicSearch(Request $request)
    {
        $request->validate([
            'domain' => 'required|string|max:255',
        ]);

        $domain = $request->domain;
        $extension = '';
        
        if (str_contains($domain, '.')) {
            $parts = explode('.', $domain);
            $extension = end($parts);
            $domain = implode('.', array_slice($parts, 0, -1));
        }

        $availableExtensions = DomainPrice::where('is_active', true)
            ->orderBy('selling_price')
            ->get();

        return Inertia::render('Public/Domains/Search', [
            'domain' => $domain,
            'requestedExtension' => $extension,
            'domainPrices' => $availableExtensions,
        ]);
    }
}
