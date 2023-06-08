<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloompay USDS Gateway</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="tx.css">
    <!-- for copy to clipboard icon with tooltip -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</head>
<body>

    <div class="container my-4">

        <h1 class="mb-5">Bloompay USDS Gateway
        <div class="pagelayer-divider-holder">
            <span class="pagelayer-divider-seperator"></span>
        </div></h1>

        <br /><br />

        <div class="container h-100">
            <div class="d-flex justify-content-center h-100">
                <div class="user_card">
                    <h3 class="text-center">Merchant Login</h3> <!-- Added title -->
                    <div class="d-flex justify-content-center form_container">
                        <form action="tx.php" method="get">
                            <div class="input-group mb-3">
                                <div class="input-group-append">
                                    <span class="input-group-text"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" name="api_key" class="form-control input_user" value="" placeholder="API key">
                            </div>
                            <div class="d-flex justify-content-center mt-3 login_container">
                                <button type="submit" name="button" class="btn login_btn">Login</button>
                            </div>
                        </form>
                    </div>
                    <div class="d-flex justify-content-center mt-3">
                        <a class="btn login_btn" style="max-width: 200px;" href="trust.php">Sign up</a> <!-- Added button -->
                    </div>
                    <br />
                </div>
            </div>
        </div>
    </div>
</body>
</html>
