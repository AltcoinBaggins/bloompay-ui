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

        <div style="float: right;">
           <!-- ... -->
            <button id="withdrawButton" class="btn btn-primary" data-toggle="modal" data-target="#withdrawModal">
                Withdraw
            </button>
            <a href="https://bloompay.bloomshares.com/trust.php?api_key=<?= $api_key ?>&button=" class="btn btn-success" data-loading="Loading Wizard">
                Export Wallet
            </a><br />

            <br /> <br />
            
        </div>


        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Available USDS balance</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-wallet-usds-balance>...</b></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">BNB gas balance</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-wallet-bnb-balance>...</b></p>
                        <div id="bnb-low-balance-alert" class="alert alert-warning d-none">Warning: BNB balance is low! Recommended minimum amount is 0.01 BNB. Please top up.</div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Merchant wallet address</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-wallet-address class="highlight">...</b>
                            <img src="https://bloompay.bloomshares.com:48080/merchant/<?= $api_key ?>/wallet_address_qr" alt="Merchant Wallet" style="max-width: 100px; border: 3px solid black; float: right;" />
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Merchant API key</h5>
                        <p class="card-text" style="font-size: 24px;"><b class="highlight"><?= $api_key ?></b></p>
                    </div>
                </div>
            </div>
        </div>



        <br /><br />

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
                        <tr class="<?php echo ($transaction['status'] == 'paid' || $transaction['status'] == 'collected' || $transaction['status'] == 'complete') ? 'table-success' : ''; ?>">
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
    

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw Tokens</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="withdrawAlert" role="alert" style="display: none;"></div>
                    <form id="withdrawForm">
                        <div class="form-group">
                            <label for="recipientAddress">Recipient Address</label>
                            <input type="text" class="form-control" id="recipientAddress" placeholder="Enter address">
                            <small id="addressHelp" class="form-text text-muted">
                                Enter a valid recipient address in the format: 0x096C48E4D7BeA71059AcE1A23F3BccA6489455EE
                            </small>
                        </div>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ... -->

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
            var bnbLowBalanceAlertElement = document.getElementById('bnb-low-balance-alert');


            // Fill elements with values from the response
            walletAddressElement.textContent = response.merchant_address.address;
            walletUSDSBalanceElement.textContent = formatToBitcoinPrice(response.merchant_address.last_usds_balance);
            var bnbBalance = formatToEthereumBNBPrice(response.merchant_address.last_bnb_balance);
            walletBNBBalanceElement.textContent = formatToEthereumBNBPrice(response.merchant_address.last_bnb_balance);
            
            // Show the alert if the BNB balance is less than 0.001
            if (parseFloat(bnbBalance) < 0.001) {
                bnbLowBalanceAlertElement.classList.remove('d-none');
            }
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
    <script>
        document.addEventListener('click', function(event) {
            var target = event.target;
            if (target.matches('[data-loading]')) {
                handleElementClick(target);
            }
        });

        function handleElementClick(element) {
            var originalText = element.innerText;
            var loadingText = element.getAttribute('data-loading');

            element.innerText = loadingText + ' ...';
            element.setAttribute('disabled', 'disabled');

            // Perform any additional actions or API calls here

            // Simulating a delay of 2 seconds for demonstration purposes
            setTimeout(function() {
                element.innerText = originalText;
                element.removeAttribute('disabled');
            }, 30000);
        }
    </script>
    <script>
        // Wait for the DOM to be fully loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Get the form and the alert element
            var withdrawForm = document.getElementById('withdrawForm');
            var withdrawAlert = document.getElementById('withdrawAlert');

            // Add event listener to the form submission
            withdrawForm.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent the default form submission

                // Get the recipient address from the input field
                var recipientAddress = document.getElementById('recipientAddress').value;

                // Validate the recipient address
                if (!validateAddress(recipientAddress)) {
                    // Display an error message
                    withdrawAlert.className = 'alert alert-danger';
                    withdrawAlert.textContent = 'Invalid recipient address. Please enter a valid Ethereum address.';
                    withdrawAlert.style.display = 'block';
                    return;
                }

                // Show loading message
                withdrawAlert.className = 'alert alert-info';
                withdrawAlert.textContent = 'Loading...';
                withdrawAlert.style.display = 'block';

                // Send an API request to withdraw tokens
                var url = 'https://bloompay.bloomshares.com:48080/merchant/<?= $api_key ?>/withdraw/' + recipientAddress;
                var xhr = new XMLHttpRequest();
                xhr.open('GET', url);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        // Withdrawal success
                        withdrawAlert.className = 'alert alert-success';
                        withdrawAlert.textContent = 'Tokens successfully withdrawn.';
                    } else {
                        // Withdrawal error
                        withdrawAlert.className = 'alert alert-danger';
                        withdrawAlert.textContent = 'An error occurred while withdrawing tokens.';
                    }
                };
                xhr.onerror = function() {
                    // Request error
                    withdrawAlert.className = 'alert alert-danger';
                    withdrawAlert.textContent = 'Failed to send withdrawal request.';
                };
                xhr.send();
            });

            // Function to validate the Ethereum address format
            function validateAddress(address) {
                var addressRegex = /^(0x)?[0-9a-fA-F]{40}$/;
                return addressRegex.test(address);
            }
        });
    </script>
    <script>
    $(document).ready(function(){
        $(".highlight:not([data-no-copy])").append("<i class='far fa-clipboard fa-lg' style='color: black; margin-left: 10px; cursor: pointer;' data-toggle='tooltip' title='Copy to clipboard'></i>");

        $(document).on('click', '.fa-clipboard', function(){
            var text = $(this).parent(".highlight").text();
            var $temp = $("<input>");
            $("body").append($temp);
            $temp.val(text).select();
            document.execCommand("copy");
            $temp.remove();

            // Show tooltip
            $(this).attr('title', 'Copied')
                   .tooltip('_fixTitle')
                   .tooltip('show')
                   .attr('title', 'Copy to clipboard')
                   .tooltip('_fixTitle');

            // Hide tooltip after 3 seconds
            var _this = this;
            setTimeout(function(){
                $(_this).tooltip('hide');
            }, 3000);
        });

        // Initialize tooltips
        $('[data-toggle="tooltip"]').tooltip();
    });
    </script>
</body>
</html>
