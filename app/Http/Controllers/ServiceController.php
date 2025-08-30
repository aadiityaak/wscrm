<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Auth::guard('customer')->user()->services()
            ->with(['hostingPlan'])
            ->orderBy('created_at', 'desc')
            ->get();

        return Inertia::render('Services/Index', [
            'services' => $services,
        ]);
    }

    public function show(Service $service): Response
    {
        $this->authorize('view', $service);

        $service->load(['hostingPlan', 'customer']);

        return Inertia::render('Services/Show', [
            'service' => $service,
        ]);
    }
}
