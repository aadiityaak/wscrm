<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Service;
use Carbon\Carbon;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $now = Carbon::now();
        $thisMonth = $now->startOfMonth();
        $lastMonth = $now->copy()->subMonth()->startOfMonth();

        // Customer Statistics
        $totalCustomers = Customer::count();
        $activeCustomers = Customer::active()->count();
        $newCustomersThisMonth = Customer::where('created_at', '>=', $thisMonth)->count();
        $newCustomersLastMonth = Customer::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $thisMonth)->count();

        // Order Statistics
        $totalOrders = Order::count();
        $completedOrders = Order::where('status', 'completed')->count();
        $ordersThisMonth = Order::where('created_at', '>=', $thisMonth)->count();
        $ordersLastMonth = Order::where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $thisMonth)->count();

        // Revenue Statistics
        $totalRevenue = Order::where('status', 'completed')->sum('total_amount');
        $revenueThisMonth = Order::where('status', 'completed')
            ->where('created_at', '>=', $thisMonth)->sum('total_amount');
        $revenueLastMonth = Order::where('status', 'completed')
            ->where('created_at', '>=', $lastMonth)
            ->where('created_at', '<', $thisMonth)->sum('total_amount');

        // Service Statistics
        $totalServices = Service::count();
        $activeServices = Service::active()->count();
        $expiringSoon = Service::active()->expiringSoon(30)->count();

        // Recent Activities
        $recentOrders = Order::with(['customer', 'orderItems'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $recentCustomers = Customer::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $expiringServices = Service::with(['customer', 'hostingPlan'])
            ->active()
            ->expiringSoon(30)
            ->orderBy('expires_at', 'asc')
            ->limit(5)
            ->get();

        // Calculate growth percentages
        $customerGrowth = $newCustomersLastMonth > 0
            ? (($newCustomersThisMonth - $newCustomersLastMonth) / $newCustomersLastMonth) * 100
            : ($newCustomersThisMonth > 0 ? 100 : 0);

        $orderGrowth = $ordersLastMonth > 0
            ? (($ordersThisMonth - $ordersLastMonth) / $ordersLastMonth) * 100
            : ($ordersThisMonth > 0 ? 100 : 0);

        $revenueGrowth = $revenueLastMonth > 0
            ? (($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100
            : ($revenueThisMonth > 0 ? 100 : 0);

        return Inertia::render('Dashboard', [
            'stats' => [
                'customers' => [
                    'total' => $totalCustomers,
                    'active' => $activeCustomers,
                    'newThisMonth' => $newCustomersThisMonth,
                    'growth' => round($customerGrowth, 1),
                ],
                'orders' => [
                    'total' => $totalOrders,
                    'completed' => $completedOrders,
                    'thisMonth' => $ordersThisMonth,
                    'growth' => round($orderGrowth, 1),
                ],
                'revenue' => [
                    'total' => $totalRevenue,
                    'thisMonth' => $revenueThisMonth,
                    'growth' => round($revenueGrowth, 1),
                ],
                'services' => [
                    'total' => $totalServices,
                    'active' => $activeServices,
                    'expiringSoon' => $expiringSoon,
                ],
            ],
            'recentActivities' => [
                'orders' => $recentOrders,
                'customers' => $recentCustomers,
                'expiringServices' => $expiringServices,
            ],
        ]);
    }
}
