<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
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
                        <h6><?php echo htmlspecialchars($item['item']->get_name()); ?></h6>
                        <div class="cart-item-actions">
                            <span class="quantity-label">Quantity:</span> <span class="quantity"><?php echo htmlspecialchars($item['quantity']); ?></span>
                            <span class="price-label">Price:</span> <span class="price">$<?php echo htmlspecialchars($item['item']->get_price()); ?></span>
                            <button onclick="removeCartItem(<?php echo $item['item']->get_id(); ?>)" class="btn btn-small red"><i class="material-icons">delete</i></button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: black;">Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <!-- Cart Total -->
        <div class="row">
            <div class="col s12 total-price-section">
                <span class="card-title">Total Price: <span style="color: red;"> <?php echo '$' . $cart->get_total_cart_price(); ?> </span></span>
                <br>
                <span class="card-title">Total Price After Taxes and Shipping: <span style="color: red;"> <?php echo '$' . $cart->get_total_price_after_decoration(); ?></span></span>
            </div>
        </div>
        <!-- Payment Method Selection -->
        <div class="row">
            <div class="col s12">
                <label>
                    <input name="paymentMethod" type="radio" value="paypal" onclick="togglePaymentFields('paypal')" checked />
                    <span>PayPal</span>
                </label>
                <label>
                    <input name="paymentMethod" type="radio" value="creditCard" onclick="togglePaymentFields('creditCard')" />
                    <span>Credit Card</span>
                </label>
            </div>
        </div>

        <!-- PayPal Fields -->
        <div id="paypalFields" class="payment-fields">
            <div class="input-field">
                <input type="email" id="paypalEmail" placeholder="PayPal Email">
            </div>
            <div class="input-field">
                <input type="password" id="paypalPassword" placeholder="PayPal Password">
            </div>
        </div>

        <!-- Credit Card Fields -->
        <div id="creditCardFields" class="payment-fields" style="display: none;">
            <div class="input-field">
                <input type="text" id="cardNumber" placeholder="Card Number">
            </div>
            <div class="input-field">
                <input type="text" id="cvv" placeholder="CVV">
            </div>
            <div class="input-field">
                <input type="text" id="expiryDate" placeholder="Expiry Date (MM/YY)" maxlength="5">
            </div>
        </div>

        <!-- Checkout Button -->
        <div class="row">
            <div class="col s12">
                <button onclick="checkout()" class="btn waves-effect waves-light green">Proceed to Checkout</button>
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

        function togglePaymentFields(method) {
            // PayPal Fields
            const paypalFields = document.getElementById('paypalFields');
            const paypalInputs = paypalFields.querySelectorAll('input');

            // Credit Card Fields
            const creditCardFields = document.getElementById('creditCardFields');
            const creditCardInputs = creditCardFields.querySelectorAll('input');

            if (method === 'paypal') {
                // Show PayPal, hide Credit Card
                paypalFields.style.display = 'block';
                creditCardFields.style.display = 'none';

                // Enable PayPal inputs, disable Credit Card inputs
                paypalInputs.forEach(input => input.disabled = false);
                creditCardInputs.forEach(input => input.disabled = true);
            } else {
                // Show Credit Card, hide PayPal
                paypalFields.style.display = 'none';
                creditCardFields.style.display = 'block';

                // Enable Credit Card inputs, disable PayPal inputs
                paypalInputs.forEach(input => input.disabled = true);
                creditCardInputs.forEach(input => input.disabled = false);
            }
        }


        function checkout() {
            if (confirm('Checkout?')) {

                const paymentMethod = document.querySelector('input[name="paymentMethod"]:checked').value;
                const data = {
                    checkoutFlag: true,
                    totalPrice: <?php echo $cart->get_total_price_after_decoration(); ?>,
                    paymentMethod: paymentMethod,
                };

                if (paymentMethod === 'paypal') {
                    data.paypalEmail = document.getElementById('paypalEmail').value;
                    data.paypalPassword = document.getElementById('paypalPassword').value;
                } else {
                    data.cardNumber = document.getElementById('cardNumber').value;
                    data.cvv = document.getElementById('cvv').value;
                    data.expiryDate = document.getElementById('expiryDate').value;
                }

                $.ajax({
                    url: 'checkout',
                    type: 'POST',
                    data: data,
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