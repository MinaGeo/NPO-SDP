<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/css/materialize.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Processing Transaction</title>
    <style>
        .center-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .progress-text {
            margin-top: 20px;
            font-size: 1.5rem;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container center-container">
        <div class="preloader-wrapper active">
            <div class="spinner-layer spinner-blue-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div><div class="gap-patch">
                    <div class="circle"></div>
                </div><div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
        <div class="progress-text">
            Processing your transaction. Please wait...
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@materializecss/materialize@1.0.0/dist/js/materialize.min.js"></script>
    <script>
    window.onload = function () {
        setTimeout(function () {
            $.ajax({
                url: 'executeDonationState',
                type: 'POST',
                dataType: 'json',
                data: {
                    donationFlag: true,
                },
                success: function(response) {
                    // Parse and handle the success response
                    const res = typeof response === "string" ? JSON.parse(response) : response;
                    if (res['success']) {
                        alert('Donation successful');
                        location.href = "donationSuccess";
                    }
                },
                error: function(xhr) {
                    // Extract and alert the error message
                    let errorMessage = "An error occurred while processing your request.";
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    } else if (xhr.responseText) {
                        errorMessage = xhr.responseText;
                    }
                    alert(errorMessage);
                }
            });
        }, 1000); // 1000 milliseconds = 1 second
    };
</script>


</body>
</html>
