<?php

namespace App\Http\Controllers;

use App\Models\ServicePlan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ServicePlanController extends Controller
{
    public function index(): Response
    {
        $servicePlans = ServicePlan::where('is_active', true)
            ->orderBy('category')
            ->orderBy('price')
            ->get()
            ->groupBy('category');

        return Inertia::render('Services/Index', [
            'servicePlans' => $servicePlans,
            'categories' => [
                'web_package' => 'Paket Website',
                'addon' => 'Add-on Services',
                'license' => 'Lisensi Premium',
                'custom_system' => 'Custom System'
            ]
        ]);
    }

    public function show(ServicePlan $servicePlan): Response
    {
        return Inertia::render('Services/Show', [
            'servicePlan' => $servicePlan
        ]);
    }
}