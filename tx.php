<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloompay Merchant Dashboard</title>
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
    error_reporting(0);
    ini_set('display_errors', 0);

    $svc_url = 'https://merchants.bloompay.co.uk';
    $self_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
    $api_key = $_GET['api_key'] ?? null;

    if (!$api_key) {
        header('Location: /');
        exit();
    }

    // Validate the API key
    if (!preg_match('/^G\d{2}[a-zA-Z0-9]{55}$/', $api_key)) {
        header('Location: /?a=ik');
        exit();
    }

    $ch = curl_init();
    $url = $svc_url . '/merchant/' . $api_key . '/list_payments/all';
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $transactions = json_decode($response, true);
?>

    <div class="container my-4"<?php if ($api_key): ?> style="max-width: 100%;"<?php endif; ?>>

        <h1 class="mb-5">
            <a class="btn btn-secondary btn-sm" style="float: right;width: 100px;text-transform: none;" href="/">Log Out</a>

            Bloompay Merchant Dashboard

            <div class="pagelayer-divider-holder">
                <span class="pagelayer-divider-seperator"></span>
            </div>
        </h1>

        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Available USDS Balance</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-wallet-usds-balance>...</b></p>
                        <button id="withdrawButton" class="btn btn-primary mt-3" data-toggle="modal" data-target="#withdrawModal" data-need-usds data-need-bnb disabled>
                            Withdraw
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">BNB Gas Balance</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-wallet-bnb-balance>...</b></p>
                        <button id="topupButton" class="btn btn-primary mt-3" data-toggle="modal" data-target="#topupModal">
                            Deposit BNB
                        </button>
                        <button id="withdrawBNBButton" class="btn btn-danger mt-3 ml-2" style="opacity: 0.5;" data-toggle="modal" data-target="#withdrawBNBModal" data-need-bnb disabled>
                            Withdraw BNB
                        </button>
                        <div id="bnb-low-balance-alert" class="alert alert-warning d-none">Warning: BNB balance is low! Recommended minimum amount is 0.01 BNB. Please top up.</div>
                        <div id="bnb-low-balance-alert2" class="alert alert-warning d-none">Warning: Not enough BNB to cover gas fees, withdrawal is disabled. Minimum amount is 0.001 BNB. Please top up.</div>
                    </div>
                </div>
            </div>
<?php
    $completedCount = 0;
    foreach($transactions as $transaction) {
        if ($transaction['status'] == 'collected' || $transaction['status'] == 'complete') {
            $completedCount++;
        }
    }
?>

            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Transactions Completed</h5>
                        <p class="card-text" style="font-size: 24px;"><b data-transaction-completed><?php echo $completedCount; ?></b></p>
                        <a href="https://merchants.bloompay.co.uk/howto.php?api_key=<?= $api_key ?>" id="backupButton" class="btn btn-primary mt-3" data-loading="Loading guide">
                            Getting Started Guide
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body text-center">
                        <h5 class="card-title">Merchant API key</h5>
                        <p class="card-text" style="font-size: 15px;"><b class="highlight smaller-font"><?= $api_key ?></b></p>
                        <button id="backupButton" class="btn btn-primary mt-3" Xdata-loading="Backing up" data-toggle="modal" data-target="#backupWalletModal">
                            Backup Wallet
                        </button>
                    <!--
                        <a href="trustwallet.php?api_key=<?= $api_key ?>" id="backupButton" class="btn btn-primary mt-3">
                            Import Wallet
                        </a>
                    -->
                    </div>
                </div>
            </div>
        </div>

        <img src="qr_placeholder.png" style="height: 1px; width: 1px; opacity: 0.5; overflow: hidden; float: right; position: absolute;" />

        <br /><br />

            <h2>Transactions</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Transaction ID</th>
                        <?php /*<th scope="col">Wallet ID</th> */ ?>
                        <th scope="col">Address</th>
                        <th scope="col">USDS Required</th>
                        <th scope="col">USD Value</th>
                        <th scope="col">USDS Price</th>
                        <th scope="col">Client IP</th>
                        <th scope="col">Required Token</th>
                        <th scope="col">Created At</th>
                        <th scope="col">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($transactions as $transaction): ?>
                        <tr class="<?php echo ($transaction['status'] == 'collected') ? 'table-success' : ''; ?> <?php echo ($transaction['status'] == 'paid' || $transaction['status'] == 'complete') ? 'table-warning' : ''; ?>">
                            <th scope="row"><?php echo $transaction['transaction_id']; ?></th>
                            <?php /*<td><?php echo $transaction['wallet_id']; ?></td>*/ ?>
                            <td><?php echo $transaction['address']; ?></td>
                            <td><?php echo number_format($transaction['required_balance'], 8, '.', ''); ?></td>
                            <td><?php echo isset($transaction['metadata']['usd_amount']) ? $transaction['metadata']['usd_amount'] : ''; ?></td>
                            <td><?php echo isset($transaction['metadata']['usds_price']) ? '&dollar;' . $transaction['metadata']['usds_price'] : ''; ?></td>
                            <td><?php echo isset($transaction['metadata']['ip']) ? $transaction['metadata']['ip'] : ''; ?></td>
                            <td><?php echo $transaction['required_token']; ?></td>
                            <td><?php echo date("Y-m-d H:i:s", $transaction['created_at']); ?></td>
                            <td><?php echo $transaction['status']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
    </div>
    

    <!-- Withdraw Modal -->
    <div class="modal fade" id="withdrawModal" tabindex="-1" role="dialog" aria-labelledby="withdrawModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawModalLabel">Withdraw USDS</h5>
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
                                Enter a valid USDS address in the format: 0x096C48E4D7BeA71059AcE1A23F3BccA6489455EE
                                <br><br>
                            </small>
                            <label for="usdsAmount">Amount</label>
                            <input type="text" class="form-control" id="usdsAmount" placeholder="Enter amount">
                            <small id="usdsAmountHelp" class="form-text text-muted">
                                Maximum Amount:  <a href="javascript:void(0);" class="usdsAmount" onclick="fillUsdsAmount()">0.0001</a>
                                <br><br>
                            </small>
                            <label for="usdsCode">Google 2FA Code</label>
                            <input type="text" class="form-control" id="usdsCode" placeholder="2FA">
                        </div>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ... -->

    <!-- Withdraw BNB Modal -->
    <div class="modal fade" id="withdrawBNBModal" tabindex="-1" role="dialog" aria-labelledby="withdrawBNBModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="withdrawBNBModalLabel">Withdraw BNB</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="withdrawBNBAlert" role="alert" style="display: none;"></div>
                    <form id="withdrawBNBForm">
                        <div class="form-group">
                            <label for="bnbRecipientAddress">Recipient Address</label>
                            <input type="text" class="form-control" id="bnbRecipientAddress" placeholder="Enter address">
                            <small id="addressHelp" class="form-text text-muted">
                                Enter a valid BNB Smart Chain address in the format: 0x096C48E4D7BeA71059AcE1A23F3BccA6489455EE
                            </small>
                            <label for="bnbAmount">Amount</label>
                            <input type="text" class="form-control" id="bnbAmount" placeholder="Enter amount">
                            <small id="bnbAmountHelp" class="form-text text-muted">
                                Maximum Amount: <a href="javascript:void(0);" class="bnbAmount" onclick="fillBnbAmount()">0.0001</a>
                                <br><br>
                            </small>
                            <label for="bnbCode">Google 2FA Code</label>
                            <input type="text" class="form-control" id="bnbCode" placeholder="2FA">
                        </div>
                        <button type="submit" class="btn btn-primary">Withdraw</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <!-- ... -->

    <!-- Deposit BNB Modal -->
    <div class="modal fade" id="topupModal" tabindex="-1" role="dialog" aria-labelledby="topupModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="topupModalLabel">Deposit BNB</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="topupAlert" role="alert" style="display: none;"></div>
                    <div class="text-center">
                        <small id="addressHelp" class="form-text text-muted" style="text-align: left;">
                            Send BNB to your merchant wallet address:
                        </small>
                        <br />
                        <p><b class="highlight"><span data-wallet-address>...</span></b></p>
                        <img src="https://merchants.bloompay.co.uk/merchant/<?= $api_key ?>/wallet_address_qr" alt="Merchant Wallet" style="max-width: 125px; margin-top: 10px; float: left;" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- ... -->

    <!-- Google 2FA Modal -->
    <div class="modal fade" id="google2FAModal" tabindex="-1" role="dialog" aria-labelledby="google2FAModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="google2FAModalLabel">Set up Google 2FA</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="google2FAAlert" role="alert" style="display: none;"></div>
                    <div id="google2FASetup">
                        <p>Scan the QR code with your Google Authenticator app:</p>
                            <div class="text-center">
                                <img id="google2FAQrCodePlaceholder" src="qr_placeholder.png" alt="Placeholder" style="max-width: 200px; margin: 10px auto 30px; display: none;" />
                                <img id="google2FAQrCode" src="" alt="Google 2FA QR Code" style="max-width: 200px; margin: 10px auto 30px; display: none;" />
                            </div>
                        <form id="google2FAForm">
                            <label for="google2FACode">Enter the 6-digit code from the app:</label><br />
                            <div class="form-row">
                                <div class="col">
                                    <div class="form-group">
                                        <input type="text" class="form-control form-control-lg" id="google2FACode" placeholder="Enter 6-digit code">
                                    </div>
                                </div>
                                <div class="col">
                                    <button type="submit" class="btn btn-primary btn-lg"style="margin-top: 0px; width: 100%;">Confirm</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div id="google2FASuccess" style="display: none;">
                        <div class="text-center">
                            <i class="fas fa-check-circle fa-3x text-success"></i>
                            <p>Google 2FA has been successfully set up.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Backup Wallet Modal -->
    <div class="modal fade" id="backupWalletModal" tabindex="-1" role="dialog" aria-labelledby="backupWalletModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="backupWalletModalLabel">Backup Wallet</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="alert" id="backupWalletAlert" role="alert" style="display: none;"></div>
                    <form id="backupWalletForm">
                        <div class="form-group">
                            <label for="backupWalletCode">Google 2FA Code</label>
                            <input type="text" class="form-control" id="backupWalletCode" placeholder="2FA">
                        </div>
                        <button type="submit" class="btn btn-primary">Backup Wallet</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <footer>
      © 2022 - <?php echo date('Y'); ?> Bloomshares Ltd
    </footer>
    
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

// Helper function to format value like a Bitcoin price to 8 decimals
function formatToBitcoinPrice(value) {
  return parseFloat(value).toFixed(8);
}

// Helper function to format value like an Ethereum/BNB price to 8 decimals if necessary
function formatToEthereumBNBPrice(value) {
  var decimals = parseFloat(value).toFixed(8);
  return decimals.endsWith('.00000000') ? parseInt(decimals) : decimals;
}
function refreshBalances() {
  // Check if a request is already in progress
  if (refreshBalances.requestInProgress) {
    console.log('A request is already in progress. Please wait.');
    return;
  }

  // Set the request status to indicate that a request is in progress
  refreshBalances.requestInProgress = true;

  var apiKey = '<?= $api_key ?>';
  var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/export_wallet_info';

  // Create a promise to handle the AJAX request
  var ajaxPromise = new Promise(function (resolve, reject) {
    $.ajax({
      url: url,
      method: 'GET',
      timeout: 60000, // Set the timeout to 60 seconds (60000 milliseconds)
      success: resolve,
      error: reject,
    });
  });

  ajaxPromise
    .then(function (response) {
      // Fill elements with values from the response
      $('[data-wallet-address]').text(response.merchant_address.address);
      var usdsBalance = formatToBitcoinPrice(response.merchant_address.last_usds_balance);
      $('[data-wallet-usds-balance]').text(usdsBalance);
      $('#usdsAmount').data('max', usdsBalance);
      $('.usdsAmount').text(usdsBalance);

      //if (!$('#usdsAmount').closest('.modal').hasClass('show'))
      //    $('#usdsAmount').val(usdsBalance);

      var bnbBalance = formatToEthereumBNBPrice(response.merchant_address.last_bnb_balance);
      $('[data-wallet-bnb-balance]').text(bnbBalance);
      $('#bnbAmount').data('max', bnbBalance);
      $('.bnbAmount').text(bnbBalance);

      //if (!$('#bnbAmount').closest('.modal').hasClass('show'))
      //    $('#bnbAmount').val(bnbBalance);

      // Disable elements and show tooltips depending on the balance
      $('[data-need-bnb]').each(function () {
        if (!response.has_2fa_secret) {
          $(this)
            .addClass('disabled')
            .attr('disabled', '')
            .attr('data-original-title', 'Missing 2FA')
            .tooltip();
        } else if (parseFloat(bnbBalance) < 0.001) {
          $(this)
            .addClass('disabled')
            .attr('disabled', '')
            .attr('data-original-title', 'Insufficient BNB balance')
            .tooltip();
        } else {
          $(this)
            .removeClass('disabled')
            .removeAttr('disabled')
            .attr('data-original-title', '')
            .tooltip('dispose');
        }
      });

      $('[data-need-usds]').each(function () {
        if (!response.has_2fa_secret) {
          $(this)
            .addClass('disabled')
            .attr('disabled', '')
            .attr('data-original-title', 'Missing 2FA')
            .tooltip();
        } else if (parseFloat(response.merchant_address.last_usds_balance) === 0) {
          $(this)
            .addClass('disabled')
            .attr('disabled', '')
            .attr('data-original-title', 'Insufficient USDS balance')
            .tooltip();
        } else {
          $(this)
            .removeClass('disabled')
            .removeAttr('disabled')
            .attr('data-original-title', '')
            .tooltip('dispose');
        }
      });

      // Check if 2FA is not set up
      if (!response.has_2fa_secret) {
        if (!$('#2faWarning')[0])
          $('h1').after(
            '<div id="2faWarning" class="alert alert-warning" role="alert">You need to set up Google 2FA in order to use the withdraw function. <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#google2FAModal">Set up Google 2FA</button></div>'
          );
      } else {
        $('#2faWarning').remove();
      }

      // Check the BNB balance
      if (parseFloat(bnbBalance) < 0.001) {
        $('#bnb-low-balance-alert').addClass('d-none');
        $('#bnb-low-balance-alert2').removeClass('d-none');
      } else if (parseFloat(bnbBalance) < 0.01) {
        // Show the alert if the BNB balance is less than 0.001
        $('#bnb-low-balance-alert2').addClass('d-none');
        $('#bnb-low-balance-alert').removeClass('d-none');
      } else {
        // Hide the alert if the BNB balance is not less than 0.001
        $('#bnb-low-balance-alert').addClass('d-none');
        $('#bnb-low-balance-alert2').addClass('d-none');
      }

      // Reset the request status after the request is complete
      refreshBalances.requestInProgress = false;
    })
    .catch(function (error) {
      // Handle the failed response
      console.log('AJAX request failed:', error);

      // Reset the request status after the request is complete
      refreshBalances.requestInProgress = false;
    });
}

// Initialize the requestInProgress property
refreshBalances.requestInProgress = false;

// Example usage
refreshBalances();
setInterval(refreshBalances, 15000);


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

    // Simulating a delay of 2 seconds for demonstration purposes
    setTimeout(function() {
        element.innerText = originalText;
        element.removeAttribute('disabled');
    }, 30000);
}

// Function to validate the Ethereum address format
function validateAddress(address) {
    var addressRegex = /^(0x)?[0-9a-fA-F]{40}$/;
    return addressRegex.test(address);
}

$(document).ready(function() {
    var apiKey = '<?= $api_key ?>';

    $('#withdrawForm').on('submit', function(event) {
        event.preventDefault();

        var recipientAddress = $('#recipientAddress').val();
        var usdsAmount = $('#usdsAmount').val();
        var usdsCode = $('#usdsCode').val();

        if (!validateAddress(recipientAddress)) {
            $('#withdrawAlert').addClass('alert alert-danger').text('Invalid recipient address. Please enter a valid BNB address.').show();
            return;
        }

        if (usdsAmount == '') {
            $('#withdrawAlert').addClass('alert alert-danger').text('Please enter valid amount.').show();
            return;
        }

        if (Number(usdsAmount) > Number($('#usdsAmount').data('max'))) {
            $('#withdrawAlert').addClass('alert alert-danger').text('Not enough balance.').show();
            return;
        }

        if (!usdsCode.match(/^\d{6}$/)) {
            $('#withdrawAlert').addClass('alert alert-danger').text('Please enter a valid Google 2FA code.').show();
            return;
        }

        $('#withdrawAlert').addClass('alert alert-info').text('Loading...').show();

        var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/withdraw/' + recipientAddress + '/' + usdsAmount + '?code=' + usdsCode;
        $.getJSON(url, function(data) {
            if (data.success) {
                $('#withdrawAlert').removeClass('alert-info').removeClass('alert-danger').addClass('alert alert-success').text('Tokens successfully withdrawn.');
            } else {
                var errorMsg = data.message ? data.message : 'An error occurred while withdrawing tokens.';
                $('#withdrawAlert').removeClass('alert-info').addClass('alert alert-danger').text(errorMsg);
            }
            setTimeout(refreshBalances, 1000);
        }).fail(function() {
            $('#withdrawAlert').removeClass('alert-info').addClass('alert alert-danger').text('Failed to send withdrawal request.');
        });
    });

    $('#withdrawBNBForm').on('submit', function(event) {
        event.preventDefault();

        var bnbRecipientAddress = $('#bnbRecipientAddress').val();
        var bnbAmount = $('#bnbAmount').val();
        var bnbCode = $('#bnbCode').val();

        if (!validateAddress(bnbRecipientAddress)) {
            $('#withdrawBNBAlert').addClass('alert alert-danger').text('Invalid recipient address. Please enter a valid BNB address.').show();
            return;
        }

        if (bnbAmount == '') {
            $('#withdrawBNBAlert').addClass('alert alert-danger').text('Please enter valid amount.').show();
            return;
        }

        if (Number(bnbAmount) > Number($('#bnbAmount').data('max'))) {
            $('#withdrawBNBAlert').addClass('alert alert-danger').text('Not enough balance.').show();
            return;
        }

        if (!bnbCode.match(/^\d{6}$/)) {
            $('#withdrawBNBAlert').addClass('alert alert-danger').text('Please enter a valid Google 2FA code.').show();
            return;
        }

        $('#withdrawBNBAlert').addClass('alert alert-info').text('Loading...').show();

        var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/withdraw_bnb/' + bnbRecipientAddress + '/' + bnbAmount + '?code=' + bnbCode;
        $.getJSON(url, function(data) {
            if (data.success) {
                $('#withdrawBNBAlert').removeClass('alert-info').removeClass('alert-danger').addClass('alert alert-success').text('Successfully withdrawn BNB. Check your wallet.');
            } else {
                var errorMsg = data.message ? data.message : 'There was an issue withdrawing BNB. Please try again later.';
                $('#withdrawBNBAlert').removeClass('alert-info').addClass('alert alert-danger').text(errorMsg);
            }
            setTimeout(refreshBalances, 1000);
        }).fail(function() {
            $('#withdrawBNBAlert').removeClass('alert-info').addClass('alert alert-danger').text('Failed to send withdrawal request.');
        });
    });
});


$(document).ready(function(){
    $(".highlight:not([data-no-copy])").append("<i class='far fa-clipboard fa-lg' style='color: black; margin-left: 10px; cursor: pointer;' data-toggle='tooltip' title='Copy to clipboard'></i>");

    $(document).on('click', '.fa-clipboard', function(){
       // var text = $(this).parent(".highlight").text();
        var highlightElement = $(this).parent(".highlight");
        var text = highlightElement.text().trim();
        
        if (!text) {
            var spanElement = highlightElement.find('span:first');
            if (spanElement.length > 0) {
                text = spanElement.text().trim();
            }
        }
        console.log('Copied: '+ text);

        var $temp = $("<input>");
        var modal = $(this).closest('.modal');
        if (modal.length > 0) {
            modal.find('.modal-body').append($temp);
        } else {
            $('body').append($temp);
        }
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


// For "disabled" things
document.addEventListener('DOMContentLoaded', function () {
    // Find all elements that could be disabled
    var clickableElements = Array.from(document.querySelectorAll('a, button'));

    clickableElements.forEach(function(element) {
        element.addEventListener('click', function(e) {
            // If the element has the 'disabled' class, prevent the default action
            if (element.classList.contains('disabled')) {
                e.preventDefault();
            }
        });
    });
});

// Wallet Backup
$(document).on('submit', '#backupWalletForm', function(event) {
    event.preventDefault();

    var backupWalletCode = $('#backupWalletCode').val();

    if (!backupWalletCode.match(/^\d{6}$/)) {
        $('#backupWalletAlert').addClass('alert alert-danger').text('Please enter a valid Google 2FA code.').show();
        return;
    }

    $('#backupWalletAlert').addClass('alert alert-info').text('Validating...').show();

    var apiKey = '<?= $api_key ?>';
    var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/test_2fa?code=' + backupWalletCode;
    $.getJSON(url, function(data) {
        if (data.success) {
            $('#backupWalletAlert').removeClass('alert-info').removeClass('alert-danger').addClass('alert alert-success').text('2FA code validated successfully.');
            $('#backupWalletModal').modal('hide');
            window.location.href = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/export_wallet?code=' + backupWalletCode;
        } else {
            $('#backupWalletAlert').removeClass('alert-info').addClass('alert alert-danger').text('Invalid 2FA code. Please try again.');
        }
    });
});


// GOOGLE 2FA
$(document).ready(function() {
    var apiKey = '<?= $api_key ?>';
    var secret;
    var label = 'merchant ' + apiKey.substring(0, 4) + '-' + apiKey.substring(apiKey.length - 4);

    $(document).on('show.bs.modal', '#google2FAModal', function() {
        var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/generate_2fa';
        $('#google2FAQrCode').hide();
        $('#google2FAQrCodePlaceholder').show();
        //$('#google2FALoadingSpinner').show();
        $.getJSON(url, function(data) {
            secret = data.secret;
            var qrCodeUrl = 'https://merchants.bloompay.co.uk/merchant/2fa_qr_code?secret=' + secret + '&label=' + encodeURIComponent(label);
            $('#google2FAQrCode').attr('src', qrCodeUrl).on('load', function() {
                //$('#google2FALoadingSpinner').hide();
                $('#google2FAQrCodePlaceholder').hide();
                $('#google2FAQrCode').show();
            });
        });
    });

    $(document).on('submit', '#google2FAForm', function(event) {
        event.preventDefault();
        var code = $('#google2FACode').val();
        var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/test_2fa?code=' + code;
        $.getJSON(url, function(data) {
            if (data.success) {
                var url = 'https://merchants.bloompay.co.uk/merchant/' + apiKey + '/set_2fa_confirmed';
                $.getJSON(url, function(data) {
                    if (data.success) {
                        $('#google2FASetup').hide();
                        $('#google2FAAlert').hide();
                        $('#google2FASuccess').show();
                        refreshBalances();
                    } else {
                        $('#google2FAAlert').show();
                        $('#google2FAAlert').addClass('alert alert-danger').text('An error occurred while setting up 2FA.').show();
                    }
                });
            } else {
                $('#google2FAAlert').show();
                $('#google2FAAlert').addClass('alert alert-danger').text('Invalid 2FA code. Please try again.').show();
            }
        });
    });
});


// Copy MAX AMOUNT value
function fillUsdsAmount() {
    var usdsAmount = document.getElementById("usdsAmount");
    var spanAmount = document.querySelector(".usdsAmount");

    usdsAmount.value = spanAmount.innerText;
}

function fillBnbAmount() {
    var bnbAmount = document.getElementById("bnbAmount");
    var spanAmount = document.querySelector(".bnbAmount");

    bnbAmount.value = spanAmount.innerText;
}

// Reset modals after close
$(document).on('hidden.bs.modal', '.modal', function () {
  $(this).find('.modal-body').find('input[type="text"], input[type="number"]').val('');
  $(this).find('.modal-body').find('.alert').hide();
});

    </script>
</body>
</html>
