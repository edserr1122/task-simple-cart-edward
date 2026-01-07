<?php

namespace App\Jobs;

use App\Mail\DailySalesReport;
use App\Models\Order;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendDailySalesReport implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $adminEmail = config('products.admin_email', 'admin@example.com');

        if (! $adminEmail) {
            return;
        }

        // Get today's orders
        $orders = Order::with(['orderItems.product', 'user'])
            ->whereDate('created_at', today())
            ->get();

        // Calculate sales summary
        $totalOrders = $orders->count();
        $totalRevenue = $orders->sum('total');
        $totalItemsSold = $orders->sum(function ($order) {
            return $order->orderItems->sum('quantity');
        });

        // Group products sold today
        $productsSold = [];
        foreach ($orders as $order) {
            foreach ($order->orderItems as $orderItem) {
                $productId = $orderItem->product_id;
                $productName = $orderItem->product->name;

                if (! isset($productsSold[$productId])) {
                    $productsSold[$productId] = [
                        'name' => $productName,
                        'quantity' => 0,
                        'revenue' => 0,
                    ];
                }

                $productsSold[$productId]['quantity'] += $orderItem->quantity;
                $productsSold[$productId]['revenue'] += $orderItem->subtotal;
            }
        }

        // Send email
        Mail::to($adminEmail)->send(
            new DailySalesReport(
                today(),
                $orders,
                $productsSold,
                $totalOrders,
                $totalRevenue,
                $totalItemsSold
            )
        );
    }
}
