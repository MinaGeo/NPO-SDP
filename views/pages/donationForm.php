<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Form</title>
    <!-- Materialize CSS -->
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/eventStyle.css">

    <style>
        #modalMessage {
            color: black !important;
        }

        /* Custom Styles */
        .logo {
            width: 20px;
            vertical-align: middle;
        }

        .section {
            margin-bottom: 40px;
        }

        .input-field input:focus+label,
        .input-field textarea:focus+label {
            color: #26a69a;
        }

        .input-field input:focus,
        .input-field textarea:focus {
            border-bottom: 1px solid #26a69a;
        }

        /* Hide payment fields by default */
        .payment-fields {
            display: none;
        }

        /* Center form and adjust spacing */
        .container {
            max-width: 600px;
            margin: 0 auto;
        }

        h1 {
            text-align: center;
            color: #26a69a;
        }

        /* Styling for the submit button */
        .btn {
            background-color: #26a69a;
            color: white;
        }

        .response-message {
            margin-top: 20px;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <div class="container">
        <h1>Donation Form</h1>
        <form id="donationForm" onsubmit="event.preventDefault(); submitDonation();">
            <!-- Donator's Name -->
            <div class="input-field">
                <input type="text" id="donatorName" placeholder="Your Name" required>
            </div>

            <!-- Donation Type -->
            <div class="section">
                <label>Select Donation Type:</label>
                <p>
                    <label>
                        <input name="donationType" type="radio" value="monetary" checked />
                        <span>Monetary Donation</span>
                    </label>
                </p>
                <p>
                    <label>
                        <input name="donationType" type="radio" value="nonMonetary" />
                        <span>Non-Monetary Donation</span>
                    </label>
                </p>
            </div>

            <!-- Monetary Donation Fields -->
            <div id="monetaryFields" class="section">
                <div class="input-field">
                    <input type="number" id="amount" step="0.01" min="0" placeholder="Donation Amount ($):">
                </div>
            </div>

            <!-- Non-Monetary Donation Fields -->
            <div id="nonMonetaryFields" class="section">
                <div class="input-field">
                    <input type="text" id="donatedItem" placeholder="Item to Donate:">
                </div>
            </div>

            <!-- Payment Method -->
            <div id="monetaryFields2">
                <div class="section">
                    <label>Payment Method</label><br>
                    <p>
                        <label>
                            <input name="paymentType" type="radio" value="paypal" checked />
                            <span>PayPal</span>
                        </label>
                    </p>
                    <p>
                        <label>
                            <input name="paymentType" type="radio" value="creditCard" />
                            <span>Credit Card</span>
                        </label>
                    </p>
                </div>

                <!-- PayPal Fields -->
                <div id="paypalFields" class="payment-fields">
                    <div class="input-field">
                        <input type="email" id="paypalEmail" placeholder="PayPal Email:">
                    </div>
                    <div class="input-field">
                        <input type="password" id="paypalPassword" placeholder="PayPal Password:">
                    </div>
                </div>

                <!-- Credit Card Fields -->
                <div id="creditCardFields" class="payment-fields">
                    <div class="input-field">
                        <input type="text" id="cardNumber" placeholder="Card Number:">
                    </div>
                    <div class="input-field">
                        <input type="text" id="cvv" placeholder="CVV:">
                    </div>
                    <div class="input-field">
                        <input type="text" id="expiryDate" placeholder=" Expiry Date: MM/YY" maxlength="5">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="center-align">
                <button type="button" onclick="submitDonation()" class="btn waves-effect waves-light">Submit Donation</button>
            </div>
        </form>

        <div class="response-message"></div>
    </div>

    <!-- Success Modal Structure -->
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h4>Donation Successful!</h4>
            <p id="modalMessage"></p>
        </div>
        <div class="modal-footer">
            <a href="#!" class="modal-close waves-effect waves-green btn-flat">Close</a>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Handle Donation Type Selection (Monetary vs Non-Monetary)
            $('input[name="donationType"]').change(function() {
                if ($(this).val() === 'monetary') {
                    // Show monetary fields and hide non-monetary fields
                    $('#monetaryFields').show();
                    $('#monetaryFields2').show();
                    $('#nonMonetaryFields').hide();

                    // Clear non-monetary fields
                    $('#donatedItem').val('');
                } else {
                    // Show non-monetary fields and hide monetary fields
                    $('#monetaryFields').hide();
                    $('#monetaryFields2').hide();
                    $('#nonMonetaryFields').show();
                    $('#cardNumber').val('');
                    $('#cvv').val('');
                    $('#expiryDate').val('');
                    $('#paypalEmail').val('');
                    $('#paypalPassword').val('');
                    $('#amount').val('');
                }
            });


            // Handle Payment Method Selection (PayPal vs Credit Card)
            $('input[name="paymentType"]').change(function() {
                // Hide both payment sections by default
                $('#paypalFields').hide();
                $('#creditCardFields').hide();

                // Show the selected payment method's form fields
                if ($(this).val() === 'paypal') {
                    $('#paypalFields').show();
                } else {
                    $('#creditCardFields').show();
                }
            });

            // Trigger initial change events
            $('input[name="donationType"]:checked').trigger('change');
            $('input[name="paymentType"]:checked').trigger('change');
        });

        // Handle the form submission
        function submitDonation() {
            const donatorName = $('#donatorName').val();
            const donationType = $('input[name="donationType"]:checked').val();
            const paymentType = $('input[name="paymentType"]:checked').val();
            const amount = $('#amount').val();
            const donatedItem = $('#donatedItem').val();
            const paypalEmail = $('#paypalEmail').val();
            const paypalPassword = $('#paypalPassword').val();
            const cardNumber = $('#cardNumber').val();
            const cvv = $('#cvv').val();
            const expiryDate = $('#expiryDate').val();

            if (!donatorName || !donationType || !paymentType) {
                alert("All fields are required!");
                return;
            }

            // Example of sending the data to the server (using Ajax for submission)
            $.ajax({
                url: 'collectDonationData',
                type: 'POST',
                data: {
                    donationFlag: true,
                    donatorName: donatorName,
                    donationType: donationType,
                    amount: amount,
                    donatedItem: donatedItem,
                    paymentType: paymentType,
                    paypalEmail: paypalEmail,
                    paypalPassword: paypalPassword,
                    cardNumber: cardNumber,
                    cvv: cvv,
                    expiryDate: expiryDate
                },
                success: function(response) {
                    console.log(response);
                    const res = JSON.parse(response);
                    if (res['success']) {
                        if (res['Popup']) {
                            // For Non-Monetary Donations, show the item being donated
                            $('#modalMessage').text('Thank you for your generous non-monetary donation of "' + donatedItem + '"! We will collect it soon.');
                        } else {
                            // For Monetary Donations, show the donation amount
                            $('#modalMessage').text('Thank you for your generous monetary donation of $' + amount + '. Your contribution is greatly appreciated!');
                        }

                        var modal = document.getElementById('successModal');
                        var instance = M.Modal.init(modal);
                        instance.open();
                    } else {
                        $('#modalMessage').text('Donation failed. Please try again.');
                        var modal = document.getElementById('successModal');
                        var instance = M.Modal.init(modal);
                        instance.open();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", error);
                    alert("Something went wrong. Please try again.");
                }
            });
        }
    </script>
</body>

</html>