<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <style>
        /* Style adjustments for cart items */
        .item-detail-label {
            font-weight: bold;
            margin-right: 5px;
        }

        .item-detail-value {
            color: red;
            display: inline;
        }

        /* List styling for cart items */
        .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .cart-item h6 {
            margin: 0;
            flex: 1;
        }

        .cart-item-actions {
            display: flex;
            align-items: center;
        }

        .quantity-label,
        .price-label {
            font-weight: bold;
            color: #555;
            margin-right: 5px;
        }

        .quantity,
        .price {
            margin-right: 20px;
            color: #333;
        }

        .total-price-section {
            margin-top: 20px;
            padding: 15px;
            border-top: 2px solid #e0e0e0;
            text-align: right;
            font-size: 1.2em;
        }

        .total-price {
            font-weight: bold;
            color: green;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <div class="container">
        <div class="row">
            <div class="col s12">
                <h4>Your Cart</h4>
            </div>
        </div>

        <!-- Cart Items List -->
        <div class="row">
            <?php if (!empty($cart_items)): ?>
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item">
                        <h6><?php echo htmlspecialchars($item['item']->name); ?></h6>
                        <div class="cart-item-actions">
                            <span class="quantity-label">Quantity:</span> <span class="quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                            <span class="price-label">Price:</span> <span class="price">$<?php echo htmlspecialchars($item['item']->price); ?></span>
                            <button onclick="removeCartItem(<?php echo $item['item']->id; ?>)" class="btn btn-small red"><i class="material-icons">delete</i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <!-- Cart Total -->
        <div class="row">
            <div class="col s12 total-price-section">
                <span class="card-title">Total Price: <?php echo '$' . $cart->get_total_cart_price(); ?></span>
                <br>
                <span class="card-title">Total Price After Taxes and Shipping: <?php echo '$' . $cart->get_total_price_after_decoration(); ?></span>
            </div>
        </div>
        <!-- Checkout Button -->
        <div class="row">
            <div class="col s12">
                <button
                    onclick="checkout()"
                    class="btn waves-effect waves-light green">Proceed to Checkout</button>
            </div>
        </div>
    </div>

    <script>
        function removeCartItem(itemId) {
            if (confirm('Are you sure you want to remove this item from the cart?')) {
                $.ajax({
                    url: 'removeCartItem',
                    type: 'POST',
                    data: {
                        itemId: itemId,
                        removeFromCart: true
                    },
                    success: function(response) {
                        location.reload(); // Reload to show updated cart
                    },
                    error: function(xhr, status, error) {
                        console.error("Error removing item:", error);
                    }
                });
            }
        }


        function checkout() {
            if (confirm('Checkout?')) {
                $.ajax({
                    url: 'checkout',
                    type: 'POST',
                    data: {
                        checkoutFlag: true
                    },
                    success: function(response) {
                        location.reload(); // Reload to show updated cart
                    },
                    error: function(xhr, status, error) {
                        console.error("Error checkout:", error);
                    }
                });
            }
        }
    </script>
</body>

</html>