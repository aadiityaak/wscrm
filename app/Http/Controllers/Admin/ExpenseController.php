<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Models\Invoice;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    /**
     * Display all expenses with filtering and revenue data.
     */
    public function index(): Response
    {
        // Get current year and month for revenue calculations
        $currentYear = date('Y');
        $currentMonth = date('Y-m');

        // Calculate monthly revenue from paid invoices
        $monthlyRevenue = Invoice::where('status', 'paid')
            ->where('billing_cycle', 'monthly')
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', date('m'))
            ->sum(DB::raw('amount - COALESCE(discount, 0)'));

        // Calculate yearly revenue from paid invoices
        $yearlyRevenue = Invoice::where('status', 'paid')
            ->where('billing_cycle', 'annually')
            ->whereYear('paid_at', $currentYear)
            ->sum(DB::raw('amount - COALESCE(discount, 0)'));

        // Calculate one-time revenue (setup fees) for current year
        $oneTimeRevenue = Invoice::where('status', 'paid')
            ->where('invoice_type', 'setup')
            ->whereYear('paid_at', $currentYear)
            ->sum(DB::raw('amount - COALESCE(discount, 0)'));

        // Get total revenue for current month (all types)
        $totalMonthlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $currentYear)
            ->whereMonth('paid_at', date('m'))
            ->sum(DB::raw('amount - COALESCE(discount, 0)'));

        // Get total revenue for current year (all types)
        $totalYearlyRevenue = Invoice::where('status', 'paid')
            ->whereYear('paid_at', $currentYear)
            ->sum(DB::raw('amount - COALESCE(discount, 0)'));

        return Inertia::render('Admin/Expenses/Index', [
            'monthlyExpenses' => Expense::monthly()->orderBy('next_billing')->get(),
            'yearlyExpenses' => Expense::yearly()->orderBy('next_billing')->get(),
            'oneTimeExpenses' => Expense::oneTime()->orderBy('paid_date', 'desc')->get(),
            'revenueData' => [
                'monthly' => $monthlyRevenue,
                'yearly' => $yearlyRevenue,
                'oneTime' => $oneTimeRevenue,
                'totalMonthly' => $totalMonthlyRevenue,
                'totalYearly' => $totalYearlyRevenue,
                'currentMonth' => date('F Y'),
                'currentYear' => $currentYear,
            ],
        ]);
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        Expense::create($request->validated());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil ditambahkan.');
    }

    /**
     * Update the specified expense in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $expense->update($request->validated());

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil diperbarui.');
    }

    /**
     * Remove the specified expense from storage.
     */
    public function destroy(Expense $expense): RedirectResponse
    {
        $expense->delete();

        return redirect()->route('admin.expenses.index')
            ->with('success', 'Pengeluaran berhasil dihapus.');
    }
}
