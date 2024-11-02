<!-- Straight from Salama's code -->
<!-- This file is used for testing purposes and might be subject to future changes based on the project progression -->

<html lang="en">

<head>
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login</title>
</head>

<body>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <div class="container">
        <div class="row">
            <div class="col s12 m8 offset-m2 l6 offset-l3">
                <h2 class="center-align">Enter Email and Password</h2>
                <!-- <h6 class="red-text center-align"><?php echo $msg; ?></h6> -->
                <br /><br /><!-- deng -->
                <!-- <form method="post"> -->
                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                    <div class="input-field">
                        <label for="email">Email:</label>
                        <input type="email" name="email" id="name" class="validate">
                    </div>
                    <div class="input-field">
                        <label for="password">Password:</label>
                        <input type="password" name="password" id="password" class="validate">
                    </div>
                    <div class="center-align">
                        <button class="btn waves-effect waves-light" type="submit" name="login">Login</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>

</html>