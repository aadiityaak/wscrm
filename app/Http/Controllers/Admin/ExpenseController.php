<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    /**
     * Display all expenses with filtering.
     */
    public function index(): Response
    {
        return Inertia::render('Admin/Expenses/Index', [
            'monthlyExpenses' => Expense::monthly()->orderBy('next_billing')->get(),
            'yearlyExpenses' => Expense::yearly()->orderBy('next_billing')->get(),
            'oneTimeExpenses' => Expense::oneTime()->orderBy('paid_date', 'desc')->get(),
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
