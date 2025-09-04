<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\DomainPrice;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DomainController extends Controller
{
    public function index(): Response
    {
        $domainPrices = DomainPrice::where('is_active', true)
            ->when(request('search'), function ($query, $search) {
                $query->where('extension', 'like', "%{$search}%");
            })
            ->orderBy('selling_price')
            ->get();

        return Inertia::render('Customer/Domains/Index', [
            'domainPrices' => $domainPrices,
            'filters' => request()->only(['search']),
        ]);
    }

    public function search(Request $request)
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

        return Inertia::render('Customer/Domains/Search', [
            'domain' => $domain,
            'requestedExtension' => $extension,
            'domainPrices' => $availableExtensions,
        ]);
    }
}
