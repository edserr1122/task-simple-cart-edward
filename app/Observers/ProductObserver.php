<?php

namespace App\Observers;

use App\Jobs\SendLowStockNotification;
use App\Models\Product;
use Illuminate\Support\Facades\Cache;

class ProductObserver
{
    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        // Only check if stock_quantity was changed
        if (! $product->wasChanged('stock_quantity')) {
            return;
        }

        $threshold = config('products.low_stock_threshold', 10);
        $oldStock = $product->getOriginal('stock_quantity');
        $newStock = $product->stock_quantity;

        // Check if stock crossed below the threshold (was above, now at or below)
        $crossedThreshold = $oldStock > $threshold && $newStock <= $threshold;

        // Also trigger if stock decreased and is now at or below threshold
        $decreasedToLow = $newStock < $oldStock && $newStock <= $threshold;

        if (! $crossedThreshold && ! $decreasedToLow) {
            // Clear cache if stock is back above threshold
            if ($newStock > $threshold) {
                Cache::forget("low_stock_notified_{$product->id}");
            }
            return;
        }

        // Check if notification was already sent (prevent duplicates)
        $cacheKey = "low_stock_notified_{$product->id}";
        if (Cache::has($cacheKey)) {
            return;
        }

        // Dispatch job to send notification
        SendLowStockNotification::dispatch($product);

        // Cache the notification for 8 hours to prevent duplicates
        Cache::put($cacheKey, true, now()->addHours(8));
    }
}
