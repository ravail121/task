<?php

namespace App\Services;

use App\Models\Merchant;
use App\Models\Affiliate;
use App\Models\Order;
use App\Services\AffiliateService;
use Mockery;

class OrderService
{
    protected $affiliateService;

    public function __construct(AffiliateService $affiliateService)
    {
        $this->affiliateService = $affiliateService;
    }

    public function processOrder(array $data)
    {
        $merchant = Merchant::where('domain', $data['merchant_domain'])->firstOrFail();
        $subtotal = $data['subtotal_price'];

        $existingOrder = Order::where('external_order_id', $data['order_id'])->first();
        if ($existingOrder) {
            return $existingOrder;
        }
        
        $affiliate = $this->affiliateService->register(
            $merchant,
            $data['customer_email'],
            $data['customer_name'],
            0.1
        );

        $affiliate = Affiliate::where([
            'merchant_id' => $merchant->id,
            'discount_code' => $data['discount_code']
        ])->firstOrFail();
        
        $order = new Order([
            'subtotal' => $subtotal,
            'commission_owed' => $subtotal * $affiliate->commission_rate,
            'payout_status' => Order::STATUS_UNPAID,
            'external_order_id' => $data['order_id'],
            'customer_email' => $data['customer_email'],
            'discount_code' => $data['discount_code']
        ]);
        
        $order->merchant()->associate($merchant);
        $order->affiliate()->associate($affiliate);
        $order->save();
       
        return $order;
    }
}