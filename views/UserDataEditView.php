<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Edit User Data</title>
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
        function updateUserData() {
            const firstName = $('#firstName').val();
            const lastName = $('#lastName').val();
            const email = $('#email').val();
            const password = $('#password').val();

            if (!firstName || !lastName || !email || !password) {
                alert('All fields must not be empty!');
                return;
            }

            $.ajax({
                url: 'updateUserData', // URL for your update endpoint
                type: 'POST',
                data: {
                    registerFlag: true,
                    firstName: firstName,
                    lastName: lastName,
                    email: email,
                    password: password
                },
                success: function(response) {
                    const res = JSON.parse(response);
                    alert(res['message']);
                    location.reload(); // Reloads the page to reflect changes
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    // alert('An error occurred! Please try again.');
                }
            });
        }
    </script>

    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <h2 style="color: black;" class="center-align">Profile Data</h2>
                <br />
                <!-- User Data Edit Form -->
                <form id="edit-user-form" method="POST">
                    <?php if (isset($userData)): ?>
                        <div class="input-field">
                            <label for="firstName">First Name:</label>
                            <input type="text" name="firstName" id="firstName" class="validate" value="<?= htmlspecialchars($userData['firstName']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="lastName">Last Name:</label>
                            <input type="text" name="lastName" id="lastName" class="validate" value="<?= htmlspecialchars($userData['lastName']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="email">Email:</label>
                            <input type="email" name="email" id="email" class="validate" value="<?= htmlspecialchars($userData['email']); ?>" required>
                        </div>
                        <div class="input-field">
                            <label for="password">Password:</label>
                            <input type="password" name="password" id="password" class="validate" required>
                        </div>
                        <div class="center-align">
                            <div onclick="updateUserData()" class="btn waves-effect waves-light">
                                Save Changes
                            </div>
                        </div>
                    <?php else: ?>
                        <p>No user data available.</p>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <!-- <div class="center-align" style="margin-top: 20px;">
            <p style="font-size: small;">
                Finished updating your info? 
                <a href="login">Log in</a>
            </p>
        </div> -->
    </div>
</body>
</html>
