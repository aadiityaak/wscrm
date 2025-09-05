<?php

namespace App\Http\Controllers;

use App\Models\DomainPrice;
use App\Models\HostingPlan;
use App\Models\ServicePlan;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderSimulatorController extends Controller
{
    public function index(): Response
    {
        // Get available options for the simulator
        $domainPrices = DomainPrice::orderBy('extension')
            ->get()
            ->map(function ($domain) {
                return [
                    'id' => $domain->id,
                    'extension' => $domain->extension,
                    'price' => $domain->selling_price,
                    'label' => $domain->extension.' - Rp '.number_format($domain->selling_price, 0, ',', '.').'/tahun',
                ];
            });

        $hostingPlans = HostingPlan::where('is_active', true)
            ->orderBy('plan_name')
            ->orderBy('storage_gb')
            ->get()
            ->map(function ($plan) {
                return [
                    'id' => $plan->id,
                    'plan_name' => $plan->plan_name,
                    'storage_gb' => $plan->storage_gb,
                    'cpu_cores' => $plan->cpu_cores,
                    'ram_gb' => $plan->ram_gb,
                    'price' => $plan->selling_price,
                    'label' => $plan->plan_name.' ('.$plan->storage_gb.'GB) - Rp '.number_format($plan->selling_price, 0, ',', '.'),
                ];
            });

        $servicePlans = ServicePlan::where('is_active', true)
            ->orderBy('category')
            ->orderBy('price')
            ->get()
            ->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'category' => $service->category,
                    'price' => $service->price,
                    'description' => $service->description,
                    'label' => $service->name.' - '.($service->price > 0 ? 'Rp '.number_format($service->price, 0, ',', '.') : 'Hubungi Kami'),
                ];
            });

        return Inertia::render('OrderSimulator/Index', [
            'domainPrices' => $domainPrices,
            'hostingPlans' => $hostingPlans,
            'servicePlans' => $servicePlans,
        ]);
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'domain_id' => 'nullable|exists:domain_prices,id',
            'hosting_id' => 'nullable|exists:hosting_plans,id',
            'service_ids' => 'nullable|array',
            'service_ids.*' => 'exists:service_plans,id',
            'discount_type' => 'nullable|in:percent,nominal',
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'discount_nominal' => 'nullable|numeric|min:0',
            'domain_name' => 'nullable|string',
        ]);

        $subtotal = 0;
        $items = [];

        // Calculate domain cost
        if ($request->domain_id) {
            $domain = DomainPrice::find($request->domain_id);
            $domainName = $request->domain_name ?: 'example'.$domain->extension;

            $items[] = [
                'type' => 'domain',
                'name' => $domainName,
                'description' => 'Domain Registration (1 Year)',
                'price' => $domain->selling_price,
                'quantity' => 1,
                'total' => $domain->selling_price,
            ];
            $subtotal += $domain->selling_price;
        }

        // Calculate hosting cost
        if ($request->hosting_id) {
            $hosting = HostingPlan::find($request->hosting_id);

            $items[] = [
                'type' => 'hosting',
                'name' => $hosting->plan_name,
                'description' => $hosting->storage_gb.'GB Storage, '.$hosting->cpu_cores.' CPU, '.$hosting->ram_gb.'GB RAM (1 Year)',
                'price' => $hosting->selling_price,
                'quantity' => 1,
                'total' => $hosting->selling_price,
            ];
            $subtotal += $hosting->selling_price;
        }

        // Calculate service costs
        if ($request->service_ids) {
            $services = ServicePlan::whereIn('id', $request->service_ids)->get();

            foreach ($services as $service) {
                $items[] = [
                    'type' => 'service',
                    'name' => $service->name,
                    'description' => $service->description ?: 'Service Package',
                    'price' => $service->price,
                    'quantity' => 1,
                    'total' => $service->price,
                ];
                $subtotal += $service->price;
            }
        }

        // Calculate discount
        $discountType = $request->discount_type ?: 'percent';
        $discountPercent = 0;
        $discountAmount = 0;

        if ($discountType === 'percent' && $request->discount_percent) {
            $discountPercent = $request->discount_percent;
            $discountAmount = ($subtotal * $discountPercent) / 100;
        } elseif ($discountType === 'nominal' && $request->discount_nominal) {
            $discountAmount = min($request->discount_nominal, $subtotal); // Don't exceed subtotal
            $discountPercent = $subtotal > 0 ? ($discountAmount / $subtotal) * 100 : 0;
        }

        $total = $subtotal - $discountAmount;

        // Calculate tax (temporarily set to 0%)
        $taxPercent = 0;
        $taxAmount = ($total * $taxPercent) / 100;
        $grandTotal = $total + $taxAmount;

        return response()->json([
            'success' => true,
            'calculation' => [
                'items' => $items,
                'subtotal' => $subtotal,
                'discount_type' => $discountType,
                'discount_percent' => $discountPercent,
                'discount_amount' => $discountAmount,
                'after_discount' => $total,
                'tax_percent' => $taxPercent,
                'tax_amount' => $taxAmount,
                'grand_total' => $grandTotal,
            ],
        ]);
    }

    public function downloadPdf(Request $request)
    {
        $calculation = json_decode($request->calculation, true);
        
        if (!$calculation) {
            return response()->json(['error' => 'Invalid calculation data'], 400);
        }

        $pdf = Pdf::loadView('pdf.order-summary', [
            'calculation' => $calculation,
            'date' => now()->format('d F Y'),
            'time' => now()->format('H:i:s'),
        ]);

        return $pdf->download('Order-Summary-' . now()->format('Y-m-d') . '.pdf');
    }
}
