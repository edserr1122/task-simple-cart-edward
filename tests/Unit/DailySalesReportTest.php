<?php

namespace Tests\Unit;

use App\Jobs\SendDailySalesReport;
use App\Mail\DailySalesReport;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class DailySalesReportTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Config::set('products.admin_email', 'admin@example.com');
    }

    public function test_daily_sales_report_sends_email_when_there_are_orders_today(): void
    {
        Mail::fake();

        // Create user
        $user = User::factory()->create();

        // Create products
        $product1 = Product::factory()->create([
            'name' => 'Test Product 1',
            'price' => 10.00,
        ]);
        $product2 = Product::factory()->create([
            'name' => 'Test Product 2',
            'price' => 20.00,
        ]);

        // Create today's orders
        $order1 = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'product_id' => $product1->id,
            'quantity' => 2,
            'price' => 10.00,
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 20.00,
        ]);

        // Execute the job
        $job = new SendDailySalesReport();
        $job->handle();

        // Assert email was sent
        Mail::assertSent(DailySalesReport::class, function ($mail) {
            return $mail->hasTo('admin@example.com')
                && $mail->totalOrders === 1
                && $mail->totalItemsSold === 3
                && $mail->totalRevenue === 40.00;
        });
    }

    public function test_daily_sales_report_sends_email_when_there_are_no_orders_today(): void
    {
        Mail::fake();

        // Execute the job with no orders
        $job = new SendDailySalesReport();
        $job->handle();

        // Assert email was sent to admin with correct zero values
        Mail::assertSent(DailySalesReport::class, function ($mail) {
            if (! $mail->hasTo('admin@example.com')) {
                return false;
            }

            return $mail->totalOrders === 0
                && $mail->totalItemsSold === 0
                && abs($mail->totalRevenue - 0.0) < 0.01; // Use float comparison
        });
    }

    public function test_daily_sales_report_groups_products_correctly(): void
    {
        Mail::fake();

        // Create user
        $user = User::factory()->create();

        // Create product
        $product = Product::factory()->create([
            'name' => 'Test Product',
            'price' => 15.00,
        ]);

        // Create two orders with the same product
        $order1 = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order1->id,
            'product_id' => $product->id,
            'quantity' => 3,
            'price' => 15.00,
        ]);

        $order2 = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order2->id,
            'product_id' => $product->id,
            'quantity' => 2,
            'price' => 15.00,
        ]);

        // Execute the job
        $job = new SendDailySalesReport();
        $job->handle();

        // Assert email was sent with grouped products
        Mail::assertSent(DailySalesReport::class, function ($mail) use ($product) {
            $productsSold = $mail->productsSold;
            $productId = $product->id;

            return isset($productsSold[$productId])
                && $productsSold[$productId]['quantity'] === 5
                && $productsSold[$productId]['revenue'] === 75.00
                && $productsSold[$productId]['name'] === 'Test Product';
        });
    }

    public function test_daily_sales_report_ignores_orders_from_previous_days(): void
    {
        Mail::fake();

        // Create user
        $user = User::factory()->create();

        // Create product
        $product = Product::factory()->create();

        // Create order from yesterday
        $yesterdayOrder = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now()->subDay(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $yesterdayOrder->id,
            'product_id' => $product->id,
            'quantity' => 5,
            'price' => 10.00,
        ]);

        // Execute the job
        $job = new SendDailySalesReport();
        $job->handle();

        // Assert email shows no orders (only today's orders are counted)
        Mail::assertSent(DailySalesReport::class, function ($mail) {
            return $mail->totalOrders === 0;
        });
    }

    public function test_daily_sales_report_does_not_send_when_admin_email_not_configured(): void
    {
        Mail::fake();

        // Clear admin email config
        Config::set('products.admin_email', null);

        // Create order
        $user = User::factory()->create();
        $product = Product::factory()->create();
        $order = Order::factory()->create([
            'user_id' => $user->id,
            'created_at' => now(),
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'quantity' => 1,
            'price' => 10.00,
        ]);

        // Execute the job
        $job = new SendDailySalesReport();
        $job->handle();

        // Assert no email was sent
        Mail::assertNothingSent();
    }
}

