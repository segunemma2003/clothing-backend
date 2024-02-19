<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation</title>
</head>
<body>
    <h1>Order Confirmation</h1>

    <p>Hello {{ $user->name }},</p>

    <p>Thank you for placing your order. Below are the details:</p>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Size</th>
                <th>Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->products as $item)
            <tr>
                <td>{{ $item->product->name }}</td>
                <td>{{ $item->size }}</td>
                <td>{{ $item->total_price }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <p><strong>Total Price:</strong> NGN{{ $order->total_price }}</p>

    <h2>Shipping Address:</h2>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Phone:</strong> {{ $user->phone }}</p>
    <p><strong>Address:</strong> {{ $order->address }}</p>
    <p><strong>Payment Method:</strong> ${{ $order->payment_method }}</p>
    <p><strong>Type of Delivery:</strong> ${{ $order->type_of_delivery }}</p>

    <p>Your order is being processed and will be shipped to you soon.</p>

    <p>Thank you for shopping with us.</p>
</body>
</html>
