<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Order;

class ReportController extends Controller
{
    public function orders(Request $req)
{
    $type = $req->type ?? 'monthly';       
    $filter = $req->filter ?? null;        
    $year = now()->year;

 
    $base = Order::query();
    $detailBase = Order::query();

    if ($type === 'weekly') {
       
        $base->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);
        $detailBase->whereBetween('created_at', [now()->subDays(6)->startOfDay(), now()->endOfDay()]);

        if ($filter) {
            $base->whereRaw('DAYOFWEEK(created_at) = ?', [$filter + 1]); 
            $detailBase->whereRaw('DAYOFWEEK(created_at) = ?', [$filter + 1]);
        }

        $reports = $base->selectRaw('DATE(created_at) as period,
            COUNT(id) as total_orders,
            SUM(order_qty) as total_items,
            SUM(order_total) as total_income')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn($r) => [
                'label' => Carbon::parse($r->period)->translatedFormat('l d M'),
                'total_orders' => (int)$r->total_orders,
                'total_items' => (int)$r->total_items,
                'total_income' => (float)$r->total_income,
            ]);

    } elseif ($type === 'monthly') {
        if ($filter) $base->whereMonth('created_at', $filter);
        $base->whereYear('created_at', $year);
        $detailBase = clone $base;

        $reports = $base->selectRaw('DATE_FORMAT(created_at,"%Y-%m") as period,
            COUNT(id) as total_orders,
            SUM(order_qty) as total_items,
            SUM(order_total) as total_income')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn($r) => [
                'label' => Carbon::createFromFormat('Y-m', $r->period)->translatedFormat('F Y'),
                'total_orders' => (int)$r->total_orders,
                'total_items' => (int)$r->total_items,
                'total_income' => (float)$r->total_income,
            ]);

    } else { 
        if ($filter) $base->whereYear('created_at', $filter);
        $reports = $base->selectRaw('YEAR(created_at) as period,
            COUNT(id) as total_orders,
            SUM(order_qty) as total_items,
            SUM(order_total) as total_income')
            ->groupBy('period')
            ->orderBy('period')
            ->get()
            ->map(fn($r) => [
                'label' => 'Tahun ' . $r->period,
                'total_orders' => (int)$r->total_orders,
                'total_items' => (int)$r->total_items,
                'total_income' => (float)$r->total_income,
            ]);
    }

    $orderDetails = $detailBase->with('items')->latest()->get()->map(function ($o) {
        return [
            'customer_name' => $o->customer_name,
            'order_ref'     => $o->order_ref,
            'total_price'   => (float)$o->order_total,
            'items'         => $o->items->map(fn($i) => ['item_name'=>$i->name,'qty'=>$i->quantity])
        ];
    });

    return response()->json([
        'results' => $reports,
        'summary' => [
            'total_orders' => $reports->sum('total_orders'),
            'total_items' => $reports->sum('total_items'),
            'total_income' => $reports->sum('total_income'),
        ],
        'order_details' => $orderDetails
    ]);
}
}
