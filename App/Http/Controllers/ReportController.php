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

        if ($type === 'weekly') {

            $base = Order::whereBetween('created_at', [
                now()->subDays(6)->startOfDay(),
                now()->endOfDay()
            ]);

            $reports = $base->selectRaw('
                DATE(created_at) as period,
                COUNT(id) as total_orders,
                SUM(order_qty) as total_items,
                SUM(order_total) as total_income
            ')
                ->groupBy('period')
                ->orderBy('period')
                ->get()
                ->map(fn($r) => [
                    'label' => Carbon::parse($r->period)->translatedFormat('d M'),
                    'total_orders' => (int)$r->total_orders,
                    'total_items' => (int)$r->total_items,
                    'total_income' => (float)$r->total_income,
                ]);
        } elseif ($type === 'monthly') {

            $base = Order::whereBetween('created_at', [
                now()->subMonths(11)->startOfMonth(),
                now()->endOfMonth()
            ]);

            $reports = $base->selectRaw('
                DATE_FORMAT(created_at,"%Y-%m") as period,
                COUNT(id) as total_orders,
                SUM(order_qty) as total_items,
                SUM(order_total) as total_income
            ')
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

            $reports = Order::selectRaw('
                YEAR(created_at) as period,
                COUNT(id) as total_orders,
                SUM(order_qty) as total_items,
                SUM(order_total) as total_income
            ')
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

        return response()->json([
            'results' => $reports,
            'summary' => [
                'total_orders' => $reports->sum('total_orders'),
                'total_items' => $reports->sum('total_items'),
                'total_income' => $reports->sum('total_income'),
            ]
        ]);
    }
}
