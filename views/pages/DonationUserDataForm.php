<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate</title>
    <link rel="stylesheet" href="../assets/eventStyle.css">
    <style>
        .input-field label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const elems = document.querySelectorAll('select');
            M.FormSelect.init(elems);
        });

        function submitDonation() {
            const donatorName = $('#donatorName').val();
            const donatorEmail = $('#donatorEmail').val();
            const donationType = $('#donationType').val();

            if (!donatorName || !donatorEmail || !donationType) {
                alert('All fields must be filled!');
                return;
            }

            $.ajax({
                url: 'submitDonation', // URL for your donation endpoint
                type: 'POST',
                data: {
                    donatorName: donatorName,
                    donatorEmail: donatorEmail,
                    donationType: donationType
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    alert(res['message']);
                    location.reload(); // Reloads the page to clear the form
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
    </script>

    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <h2 style="color: black;" class="center-align">User Details</h2>
                <br />
                <!-- Donation Form -->
                <form id="donation-form" method="POST">
                    <div class="input-field">
                        <label for="donatorName">Donator Name:</label>
                        <input type="text" name="donatorName" id="donatorName" class="validate" required>
                    </div>
                    <div class="input-field">
                        <label for="donatorEmail">Donator Email:</label>
                        <input type="email" name="donatorEmail" id="donatorEmail" class="validate" value="<?= htmlspecialchars($_SESSION['USER_EMAIL'] ?? ''); ?>" required>
                    </div>
                    <div class="input-field">
                        <select name="donationType" id="donationType" required>
                            <option value="" disabled selected>Choose your donation type</option>
                            <option value="money">Monetary Donation</option>
                            <option value="money">Non-Monetary Donation</option>
                        </select>
                        <label for="donationType">Donation Type:</label>
                    </div>
                    <div class="center-align">
                        <div onclick="submitDonation()" class="btn waves-effect waves-light">
                            Submit Donation
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
