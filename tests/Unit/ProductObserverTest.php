<?php

namespace Tests\Unit;

use App\Jobs\SendLowStockNotification;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ProductObserverTest extends TestCase
{
    use RefreshDatabase;

    public function test_low_stock_notification_is_dispatched_when_stock_updated_to_3(): void
    {
        Queue::fake();

        // Create a product with stock above threshold (default threshold is 10)
        $product = Product::factory()->create([
            'stock_quantity' => 15,
        ]);

        // Update stock to 3 (below threshold)
        $product->update([
            'stock_quantity' => 3,
        ]);

        // Assert that the low stock notification job was dispatched
        Queue::assertPushed(SendLowStockNotification::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });
    }

    public function test_low_stock_notification_is_not_dispatched_when_stock_stays_above_threshold(): void
    {
        Queue::fake();

        // Create a product with stock above threshold
        $product = Product::factory()->create([
            'stock_quantity' => 15,
        ]);

        // Update stock but keep it above threshold
        $product->update([
            'stock_quantity' => 12,
        ]);

        // Assert that the low stock notification job was NOT dispatched
        Queue::assertNothingPushed();
    }

    public function test_low_stock_notification_is_dispatched_when_stock_crosses_threshold(): void
    {
        Queue::fake();

        // Create a product with stock above threshold
        $product = Product::factory()->create([
            'stock_quantity' => 15,
        ]);

        // Update stock to cross below threshold (from 15 to 5)
        $product->update([
            'stock_quantity' => 5,
        ]);

        // Assert that the low stock notification job was dispatched
        Queue::assertPushed(SendLowStockNotification::class, function ($job) use ($product) {
            return $job->product->id === $product->id;
        });
    }

    public function test_low_stock_notification_is_not_dispatched_on_other_field_updates(): void
    {
        Queue::fake();

        // Create a product with stock above threshold
        $product = Product::factory()->create([
            'stock_quantity' => 15,
        ]);

        // Update only the name (not stock_quantity)
        $product->update([
            'name' => 'Updated Product Name',
        ]);

        // Assert that the low stock notification job was NOT dispatched
        Queue::assertNothingPushed();
    }
}

