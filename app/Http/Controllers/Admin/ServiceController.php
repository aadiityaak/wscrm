<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $services = Service::with(['customer', 'hostingPlan'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Services/Index', [
            'services' => $services,
        ]);
    }

    public function show(Service $service): Response
    {
        $service->load([
            'customer',
            'hostingPlan'
        ]);

        return Inertia::render('Admin/Services/Show', [
            'service' => $service,
        ]);
    }

    public function update(Request $request, Service $service)
    {
        $request->validate([
            'status' => 'required|in:active,suspended,terminated,pending',
            'expires_at' => 'nullable|date',
            'auto_renew' => 'boolean',
        ]);

        $service->update($request->only(['status', 'expires_at', 'auto_renew']));

        return redirect()->back()->with('success', 'Service updated successfully!');
    }
}
