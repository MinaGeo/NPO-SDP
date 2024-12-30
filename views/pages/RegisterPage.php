<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Register</title>
    <style>
        body {
            background-color: #DAF4F5;
        }
        .input-field label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function register() {
            const firstName = $('#firstName').val();
            const lastName = $('#lastName').val();
            const email = $('#email').val();
            const password = $('#password').val();

            $.ajax({
                url: 'validateRegister',
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
                    if (res.success) {
                        alert(res.message);
                        window.location.href = "login"; // Redirect to login page after successful registration
                    } else {
                        alert(res.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('An error occurred: ' + error); // More informative error message
                }
            });
        }
    </script>

    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <h2 class="center-align">Create a new account</h2>
                <br />

                <form id="register-form" method="POST">
                    <div class="input-field">
                        <label for="firstName">First Name:</label>
                        <input type="text" name="firstName" id="firstName" class="validate" required>
                    </div>
                    <div class="input-field">
                        <label for="lastName">Last Name:</label>
                        <input type="text" name="lastName" id="lastName" class="validate" required>
                    </div>
                    <div class="input-field">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="validate" required>
                    </div>
                    <div class="input-field">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" class="validate" required>
                    </div>
                    <div class="center-align">
                        <div onclick="register()" class="btn waves-effect waves-light">
                            Register
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="center-align" style="margin-top: 20px;">
            <p style="font-size: small;">
                Already have an account?
                <a href="login">Login here</a>
            </p>