<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Low Stock Threshold
    |--------------------------------------------------------------------------
    |
    | This value determines the minimum stock quantity that triggers a low
    | stock notification. When a product's stock falls at or below this value,
    | an email notification will be sent to the admin user.
    |
    */

    'low_stock_threshold' => (int) env('LOW_STOCK_THRESHOLD', 10),

    /*
    |--------------------------------------------------------------------------
    | Admin Email
    |--------------------------------------------------------------------------
    |
    | The email address where low stock notifications will be sent.
    |
    */

    'admin_email' => env('ADMIN_EMAIL', 'admin@example.com'),
];

