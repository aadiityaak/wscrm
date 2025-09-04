<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $customer = Auth::guard('customer')->user();

        $services = $customer->services()
            ->with(['hostingPlan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentOrders = $customer->orders()
            ->with(['orderItems'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $unpaidInvoices = $customer->invoices()
            ->unpaid()
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get();

        return Inertia::render('Customer/Dashboard', [
            'customer' => $customer,
            'services' => $services,
            'recentOrders' => $recentOrders,
            'unpaidInvoices' => $unpaidInvoices,
        ]);
    }
}
