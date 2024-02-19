<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Order Received</title>
</head>
<body>
    <h1>New Order Received</h1>

    <p>Hello Admin,</p>

    <p>A new order has been placed on your website. Below are the details:</p>

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

    <p><strong>Total Price:</strong> ${{ $order->total_price }}</p>

    <h2>Shipping Address:</h2>
    <p><strong>Name:</strong> {{ $user->name }}</p>
    <p><strong>Phone:</strong> {{ $user->phone }}</p>
    <p><strong>Address:</strong> {{ $order->address }}</p>
    <p><strong>Payment Method:</strong> ${{ $order->payment_method }}</p>
    <p><strong>Type of Delivery:</strong> ${{ $order->type_of_delivery }}</p>
    <p>Please process this order as soon as possible.</p>

    <p>Thank you.</p>
</body>
</html>
