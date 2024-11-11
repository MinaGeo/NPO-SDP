<!DOCTYPE html>
<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>

    <style>
        body {
            background-color: #DAF4F5;
        }

        .btn-floating img {
            width: 24px;
            height: 24px;
        }

        .logo {
            width: 20px;
            vertical-align: middle;
        }
    </style>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script>
        function login(loginMethod) {
            const email = $('#email').val();
            const password = $('#password').val();

            if (!email || !password) {
                alert("Email and Password cannot be empty!");
                return;
            }

            $.ajax({
                url: 'validateLogin',
                type: 'POST',
                data: {
                    loginFlag: true,
                    loginMethod: loginMethod,
                    email: email,
                    password: password
                },
                success: function(response) {
                    console.log(response);
                    const res = JSON.parse(response);
                    alert(res['message']);
                    if (res['success']) {
                        location.href = 'home';
                    }
                },
                error: function(xhr, status, error) {
                    console.error("An error occurred:", error);
                }
            });
        }
    </script>
    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <h2 class="center-align">Login to our website</h2>
                <br />
                <form id="login-form">
                    <div class="input-field">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="email" class="validate" required>
                    </div>
                    <div class="input-field">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" class="validate" required>
                    </div>
                    <div class="row center-align">
                        <button type="button" onclick="login('google')" class="btn-floating btn-large waves-effect waves-light red">
                            <img class="logo" src="../assets/google.png" alt="Google Logo" />
                        </button>
                        <button type="button" onclick="login('facebook')" class="btn-floating btn-large waves-effect waves-light blue">
                            <img class="logo" src="../assets/facebook.png" alt="Facebook Logo" />
                        </button>
                        <button type="button" onclick="login('github')" class="btn-floating btn-large waves-effect waves-light white">
                            <img class="logo" src="../assets/github.png" alt="GitHub Logo" />
                        </button>
                    </div>
                    <div class="center-align">
                        <button type="button" class="btn waves-effect waves-light" onclick="login('database')">
                            <img class="logo" src="../assets/web.png" alt="Login Logo" />
                            Login with Database
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="center-align" style="margin-top: 20px;">
            <p style="font-size: small;">
                Don't have an account?
                <a href="register">Create a new account</a>
            </p>
        </div>
    </div>
</body>

</html>