<?php

namespace App\Http\Controllers;

use App\Models\HostingPlan;
use Inertia\Inertia;
use Inertia\Response;

class HostingPlanController extends Controller
{
    public function index(): Response
    {
        $hostingPlans = HostingPlan::active()
            ->orderBy('plan_name')
            ->orderBy('storage_gb')
            ->get()
            ->groupBy('plan_name');

        return Inertia::render('Hosting/Index', [
            'hostingPlans' => $hostingPlans,
        ]);
    }

    public function show(HostingPlan $hostingPlan): Response
    {
        return Inertia::render('Hosting/Show', [
            'hostingPlan' => $hostingPlan,
        ]);
    }
}
