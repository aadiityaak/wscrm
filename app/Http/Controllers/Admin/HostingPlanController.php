<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HostingPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;

class HostingPlanController extends Controller
{
    public function index(): Response
    {
        $hostingPlans = HostingPlan::query()
            ->when(request('search'), function ($query, $search) {
                $query->where('plan_name', 'like', "%{$search}%");
            })
            ->orderBy('plan_name')
            ->paginate(20)
            ->withQueryString();

        return Inertia::render('Admin/HostingPlans/Index', [
            'hostingPlans' => $hostingPlans,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Admin/HostingPlans/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|unique:hosting_plans,plan_name',
            'storage_gb' => 'required|numeric|min:0',
            'bandwidth_gb' => 'required|numeric|min:0',
            'cpu_cores' => 'required|numeric|min:1',
            'ram_gb' => 'required|numeric|min:0',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        HostingPlan::create($validated);

        return redirect()->route('admin.hosting-plans.index')
            ->with('success', 'Hosting plan created successfully.');
    }

    public function show(HostingPlan $hostingPlan): Response
    {
        return Inertia::render('Admin/HostingPlans/Show', [
            'hostingPlan' => $hostingPlan,
        ]);
    }

    public function edit(HostingPlan $hostingPlan): Response
    {
        return Inertia::render('Admin/HostingPlans/Edit', [
            'hostingPlan' => $hostingPlan,
        ]);
    }

    public function update(Request $request, HostingPlan $hostingPlan): RedirectResponse
    {
        $validated = $request->validate([
            'plan_name' => 'required|string|unique:hosting_plans,plan_name,' . $hostingPlan->id,
            'storage_gb' => 'required|numeric|min:0',
            'bandwidth_gb' => 'required|numeric|min:0',
            'cpu_cores' => 'required|numeric|min:1',
            'ram_gb' => 'required|numeric|min:0',
            'price_monthly' => 'required|numeric|min:0',
            'price_yearly' => 'required|numeric|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $hostingPlan->update($validated);

        return redirect()->route('admin.hosting-plans.index')
            ->with('success', 'Hosting plan updated successfully.');
    }

    public function destroy(HostingPlan $hostingPlan): RedirectResponse
    {
        $hostingPlan->delete();

        return redirect()->route('admin.hosting-plans.index')
            ->with('success', 'Hosting plan deleted successfully.');
    }
}