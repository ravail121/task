<?php

namespace App\Http\Controllers;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     * 
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request)
    {
        $from = $request->input('from', now()->subDay());
        $to = $request->input('to', now());
        
        $orders = Order::with('affiliate')
            ->where('merchant_id', auth()->user()->merchant->id)
            ->whereBetween('created_at', [$from, $to])
            ->get();

        $count = $orders->count();
        $revenue = $orders->sum('subtotal');
        $commissions_owed = $orders->sum(function ($order) {
            return $order->commission_owed - ($order->affiliate ? 0 : $order->commission_owed);
        });

        return [
            'count' => $count,
            'revenue' => $revenue,
            'commissions_owed' => $commissions_owed,
        ];
    }
}
