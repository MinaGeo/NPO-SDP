<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Cart History</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Your Cart History</h4>
            </div>
        </div>

        <!-- Display Cart History -->
        <?php if (!empty($cart_history)): ?>
            <?php foreach ($cart_history as $history): ?>
                <div class="card">
                    <div class="card-content">
                        <span class="card-title">Cart ID: <?php echo htmlspecialchars($history['cart']->get_id()); ?> </span>
                        <ul>
                            <?php foreach ($history['items'] as $item): ?>
                                <li>
                                    <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                                    - Quantity: <?php echo htmlspecialchars($item['quantity']); ?>
                                    - Price: $<?php echo htmlspecialchars($item['price']); ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                        <p><strong>Total Price:</strong> <span class="history-price"> $<?php echo htmlspecialchars($history['total_price']); ?></span></p>
                        <p><strong>Total Price (After Taxes & Shipping):</strong>  <span class="history-price"> $<?php echo htmlspecialchars($history['total_price_after_decoration']); ?></span></p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="color: black;">No cart history available.</p>
        <?php endif; ?>
    </div>
</body>

</html>
