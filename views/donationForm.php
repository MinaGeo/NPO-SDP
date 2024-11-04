<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donation Form</title>
</head>
<body>
    <h1>Donation Form</h1>
    <form action="index.php" method="POST">
        <label for="donatorName">Your Name:</label>
        <input type="text" name="donatorName" id="donatorName" required>

        <label for="donationType">Select Donation Type:</label>
        <select name="donationType" id="donationType" required>
            <option value="monetary">Monetary Donation</option>
            <option value="nonMonetary">Non-Monetary Donation</option>
        </select>

        <div id="monetaryFields" style="display: none;">
            <label for="amount">Donation Amount ($):</label>
            <input type="number" name="amount" id="amount" step="0.01">
        </div>

        <div id="nonMonetaryFields" style="display: none;">
            <label for="donatedItem">Item to Donate:</label>
            <input type="text" name="donatedItem" id="donatedItem">
        </div>

        <label for="paymentType">Select Payment Method:</label>
        <select name="paymentType" id="paymentType" required>
            <option value="paypal">PayPal</option>
            <option value="creditCard">Credit Card</option>
        </select>

        <div id="paypalFields" style="display: none;">
            <label for="paypalEmail">PayPal Email:</label>
            <input type="email" name="paypalEmail" id="paypalEmail">
            <label for="paypalPassword">PayPal Password:</label>
            <input type="password" name="paypalPassword" id="paypalPassword">
        </div>

        <div id="creditCardFields" style="display: none;">
            <label for="cardNumber">Card Number:</label>
            <input type="text" name="cardNumber" id="cardNumber">
            <label for="cvv">CVV:</label>
            <input type="text" name="cvv" id="cvv">
            <label for="expiryDate">Expiry Date:</label>
            <input type="text" name="expiryDate" id="expiryDate" placeholder="MM/YY">
        </div>

        <button type="submit">Submit Donation</button>
    </form>
</body>
</html>
