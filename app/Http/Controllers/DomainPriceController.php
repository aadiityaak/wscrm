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
}
