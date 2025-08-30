<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(): Response
    {
        $customers = Customer::with(['orders', 'services'])
            ->withCount(['orders', 'services'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return Inertia::render('Admin/Customers/Index', [
            'customers' => $customers,
        ]);
    }

    public function show(Customer $customer): Response
    {
        $customer->load([
            'orders.orderItems',
            'services.hostingPlan',
            'invoices'
        ]);

        return Inertia::render('Admin/Customers/Show', [
            'customer' => $customer,
        ]);
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'status' => 'required|in:active,inactive,suspended',
        ]);

        $customer->update([
            'status' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Customer status updated successfully!');
    }
}
