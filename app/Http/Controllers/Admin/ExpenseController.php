<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ExpenseController extends Controller
{
    /**
     * Display monthly expenses.
     */
    public function monthly(): Response
    {
        return Inertia::render('Admin/Expenses/Monthly', [
            'title' => 'Pengeluaran Bulanan',
            'expenses' => [
                // Mock data for now
                [
                    'id' => 1,
                    'name' => 'Lisensi Claude Pro',
                    'amount' => 20,
                    'currency' => 'USD',
                    'provider' => 'Anthropic',
                    'category' => 'Software',
                    'next_billing' => '2025-01-19',
                    'status' => 'active'
                ],
                [
                    'id' => 2,
                    'name' => 'cPanel Guard',
                    'amount' => 15,
                    'currency' => 'USD',
                    'provider' => 'cPanel',
                    'category' => 'Security',
                    'next_billing' => '2025-01-15',
                    'status' => 'active'
                ]
            ]
        ]);
    }

    /**
     * Display yearly expenses.
     */
    public function yearly(): Response
    {
        return Inertia::render('Admin/Expenses/Yearly', [
            'title' => 'Pengeluaran Tahunan',
            'expenses' => [
                // Mock data for now
                [
                    'id' => 1,
                    'name' => 'Domain License',
                    'amount' => 150,
                    'currency' => 'USD',
                    'provider' => 'Registrar',
                    'category' => 'Domain',
                    'next_billing' => '2025-12-01',
                    'status' => 'active'
                ]
            ]
        ]);
    }

    /**
     * Display one-time expenses.
     */
    public function oneTime(): Response
    {
        return Inertia::render('Admin/Expenses/OneTime', [
            'title' => 'Pengeluaran Sekali Bayar',
            'expenses' => [
                // Mock data for now
                [
                    'id' => 1,
                    'name' => 'Deposit Domain .id',
                    'amount' => 500000,
                    'currency' => 'IDR',
                    'provider' => 'PANDI',
                    'category' => 'Domain Deposit',
                    'paid_date' => '2024-12-01',
                    'status' => 'paid'
                ],
                [
                    'id' => 2,
                    'name' => 'Setup Server',
                    'amount' => 100,
                    'currency' => 'USD',
                    'provider' => 'Digital Ocean',
                    'category' => 'Infrastructure',
                    'paid_date' => '2024-11-15',
                    'status' => 'paid'
                ]
            ]
        ]);
    }
}
