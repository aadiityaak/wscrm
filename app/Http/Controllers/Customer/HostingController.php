<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\HostingPlan;
use Inertia\Inertia;
use Inertia\Response;

class HostingController extends Controller
{
    public function index(): Response
    {
        $hostingPlans = HostingPlan::active()
            ->when(request('search'), function ($query, $search) {
                $query->where('plan_name', 'like', "%{$search}%");
            })
            ->orderBy('selling_price')
            ->get();

        return Inertia::render('Customer/Hosting/Index', [
            'hostingPlans' => $hostingPlans,
            'filters' => request()->only(['search']),
        ]);
    }

    public function show(HostingPlan $hostingPlan): Response
    {
        if (! $hostingPlan->is_active) {
            abort(404);
        }

        return Inertia::render('Customer/Hosting/Show', [
            'hostingPlan' => $hostingPlan,
        ]);
    }
}
