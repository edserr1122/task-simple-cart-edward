<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Report</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
    <div style="max-width: 800px; margin: 0 auto; padding: 20px;">
        <h2 style="color: #2563eb;">Daily Sales Report</h2>
        <p style="font-size: 16px; margin-bottom: 30px;">
            <strong>Date:</strong> {{ $reportDate->format('F j, Y') }}
        </p>

        @if($totalOrders === 0)
            <div style="background-color: #f9fafb; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0; color: #666;">No sales were made today.</p>
            </div>
        @else
            <!-- Summary Section -->
            <div style="background-color: #f9fafb; padding: 20px; border-radius: 5px; margin: 20px 0;">
                <h3 style="margin-top: 0; color: #1f2937;">Summary</h3>
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Total Orders:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ number_format($totalOrders) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold;">Total Items Sold:</td>
                        <td style="padding: 8px 0; text-align: right;">{{ number_format($totalItemsSold) }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px 0; font-weight: bold; font-size: 18px;">Total Revenue:</td>
                        <td style="padding: 8px 0; text-align: right; font-size: 18px; color: #059669;">
                            ${{ number_format($totalRevenue, 2) }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- Products Sold Section -->
            <div style="margin: 30px 0;">
                <h3 style="color: #1f2937;">Products Sold Today</h3>
                <table style="width: 100%; border-collapse: collapse; border: 1px solid #e5e7eb; margin-top: 10px;">
                    <thead>
                        <tr style="background-color: #f3f4f6;">
                            <th style="padding: 12px; text-align: left; border-bottom: 2px solid #d1d5db;">Product Name</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #d1d5db;">Quantity</th>
                            <th style="padding: 12px; text-align: right; border-bottom: 2px solid #d1d5db;">Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($productsSold as $product)
                            <tr>
                                <td style="padding: 12px; border-bottom: 1px solid #e5e7eb;">{{ $product['name'] }}</td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">
                                    {{ number_format($product['quantity']) }}
                                </td>
                                <td style="padding: 12px; text-align: right; border-bottom: 1px solid #e5e7eb;">
                                    ${{ number_format($product['revenue'], 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Orders Detail Section -->
            <div style="margin: 30px 0;">
                <h3 style="color: #1f2937;">Order Details</h3>
                @foreach($orders as $order)
                    <div style="background-color: #f9fafb; padding: 15px; border-radius: 5px; margin: 15px 0; border-left: 4px solid #2563eb;">
                        <p style="margin: 0 0 10px 0;">
                            <strong>Order #{{ $order->id }}</strong> - 
                            Customer: {{ $order->user->name }} ({{ $order->user->email }})
                            <br>
                            <small style="color: #666;">Time: {{ $order->created_at->format('g:i A') }}</small>
                        </p>
                        <table style="width: 100%; margin-top: 10px;">
                            @foreach($order->orderItems as $item)
                                <tr>
                                    <td style="padding: 4px 0;">{{ $item->product->name }}</td>
                                    <td style="padding: 4px 0; text-align: right;">
                                        {{ $item->quantity }} × ${{ number_format($item->price, 2) }} = 
                                        ${{ number_format($item->subtotal, 2) }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr style="border-top: 1px solid #d1d5db; margin-top: 5px;">
                                <td style="padding: 8px 0 0 0; font-weight: bold;">Order Total:</td>
                                <td style="padding: 8px 0 0 0; text-align: right; font-weight: bold;">
                                    ${{ number_format($order->total, 2) }}
                                </td>
                            </tr>
                        </table>
                    </div>
                @endforeach
            </div>
        @endif

        <p style="margin-top: 30px; color: #666; font-size: 12px;">
            This is an automated daily sales report from your e-commerce system.
        </p>
    </div>
</body>
</html>

