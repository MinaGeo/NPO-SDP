<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Transaction Successful</title>
    <style>
        .center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
            text-align: center;
        }
        .success-icon {
            font-size: 100px;
            color: green;
        }
        .success-text {
            margin-top: 20px;
            font-size: 1.5rem;
            color: #333;
        }
    </style>
</head>
<body>
    <div class="container center-container">
        <!-- Success Icon -->
        <span class="material-icons success-icon">check_circle</span>
        
        <!-- Success Message -->
        <div class="success-text">
            Your transaction was successful! <br>
            Thank you for your purchase.
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
</body>
</html>
