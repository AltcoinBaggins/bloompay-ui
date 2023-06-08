<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloompay USDS Gateway Installation Guide</title>
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
    <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        $svc_url = 'https://bloompay.bloomshares.com:48080';
        $self_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $api_key = $_GET['api_key'] ?? null;

        if ($api_key) {
            $ch = curl_init();
            $url = $svc_url . '/merchant/' . $api_key . '/list_payments/all';
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            $transactions = json_decode($response, true);
        }
    ?>
    <div class="container my-4"<?php if ($api_key): ?> style="max-width: 100%;"<?php endif; ?>>


        <h1 class="mb-5">Bloompay Merchant Payments<!--/h1-->
        <div class="pagelayer-divider-holder">
        <span class="pagelayer-divider-seperator"></span>
        </div></h1>

        <?php if (!$api_key): ?>

    <div class="container h-100">
        <div class="d-flex justify-content-center h-100">
            <div class="user_card">
                <div class="d-flex justify-content-center form_container">
                    <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="get">
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
            </div>
        </div>
    </div>


        <?php else: ?>


        Merchant API key <b><?= $api_key ?></b><br />
        Merchant wallet address: <b data-wallet-address>...</b><br />
        Available USDS balance: <b data-wallet-usds-balance>...</b><br />
        BNB gas balance: <b data-wallet-bnb-balance>...</b><br />
        <br />
        <a href="https://bloompay.bloomshares.com/tx.php?api_key=<?= $api_key ?>&button=">
            Getting started
        </a>
        <br />

            <h2>Transactions</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Transaction ID</th>
                        <th scope="col">Wallet ID</th>
                        <th scope="col">Address</th>
                        <th scope="col">USDS Required</th>
                        <th scope="col">USD Value</th>
                        <th scope="col">Conversion price</th>
                        <th scope="col">Client IP</th>
                        <th scope="col">Required Token</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $transaction): ?>
                        <tr class="<?php echo ($transaction['status'] == 'paid' || $transaction['status'] == 'collected') ? 'table-success' : ''; ?>">
                            <th scope="row"><?php echo $transaction['transaction_id']; ?></th>
                            <td><?php echo $transaction['wallet_id']; ?></td>
                            <td><?php echo $transaction['address']; ?></td>
                            <td><?php echo number_format($transaction['required_balance'], 8, '.', ''); ?></td>
                            <td><?php echo isset($transaction['metadata']['usd_amount']) ? $transaction['metadata']['usd_amount'] : ''; ?></td>
                            <td><?php echo isset($transaction['metadata']['usds_price']) ? $transaction['metadata']['usds_price'] : ''; ?></td>
                            <td><?php echo isset($transaction['metadata']['ip']) ? $transaction['metadata']['ip'] : ''; ?></td>
                            <td><?php echo $transaction['required_token']; ?></td>
                            <td><?php echo date("Y-m-d H:i:s", $transaction['created_at']); ?></td>
                            <td><?php echo $transaction['status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    
    <!-- Copy to clipboard functionality -->
    <script>
        function copyToClipboard(elementId) {
            var aux = document.createElement("input");
            aux.setAttribute("value", document.getElementById(elementId).innerHTML);
            document.body.appendChild(aux);
            aux.select();
            document.execCommand("copy");
            document.body.removeChild(aux);
        }

        var xhr = new XMLHttpRequest();
        var url = 'https://bloompay.bloomshares.com:48080/merchant/<?= $api_key ?>/export_wallet';
        xhr.open('GET', url, true);
        xhr.responseType = 'json';

        // Set up the onload event handler
        xhr.onload = function() {
          // Check if the request was successful
          if (xhr.status === 200) {
            var response = xhr.response; // Get the response data

            // Find elements with specific data attributes
            var walletAddressElement = document.querySelector('[data-wallet-address]');
            var walletUSDSBalanceElement = document.querySelector('[data-wallet-usds-balance]');
            var walletBNBBalanceElement = document.querySelector('[data-wallet-bnb-balance]');

            // Fill elements with values from the response
            walletAddressElement.textContent = response.merchant_address.address;
            walletUSDSBalanceElement.textContent = formatToBitcoinPrice(response.merchant_address.last_usds_balance);
            walletBNBBalanceElement.textContent = formatToEthereumBNBPrice(response.merchant_address.last_bnb_balance);
          }
        };

        // Send the request
        xhr.send();

        // Helper function to format value like a Bitcoin price to 8 decimals
        function formatToBitcoinPrice(value) {
          return parseFloat(value).toFixed(8);
        }

        // Helper function to format value like an Ethereum/BNB price to 8 decimals if necessary
        function formatToEthereumBNBPrice(value) {
          var decimals = parseFloat(value).toFixed(8);
          return decimals.endsWith('.00000000') ? parseInt(decimals) : decimals;
        }

    </script>
</body>
</html>
