<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Alert</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #dc2626;">Low Stock Alert</h2>
        
        <p>This is to notify you that a product is running low on stock:</p>
        
        <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p><strong>Product Name:</strong> {{ $product->name }}</p>
            <p><strong>Current Stock:</strong> {{ $product->stock_quantity }} units</p>
            <p><strong>Price:</strong> ${{ number_format($product->price, 2) }}</p>
            <p><strong>Low Stock Threshold:</strong> {{ config('products.low_stock_threshold', 10) }} units</p>
        </div>
        
        <p style="color: #dc2626; font-weight: bold;">
            Please consider restocking this product soon.
        </p>
        
        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            This is an automated notification from your e-commerce system.
        </p>
    </div>
</body>
</html>

