<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Bloompay USDS Gateway Installation Guide</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Montserrat">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
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
        $contract = '0x83e6b68028D3F25631B2e60f7023de201c1FE996';
        $svc_url = 'https://merchants.bloompay.co.uk';
        $self_url = "https://" . $_SERVER['HTTP_HOST'] . '/';//. $_SERVER['PHP_SELF'];
        $is_new = true;

        if (isset($_GET['api_key']) && !empty($_GET['api_key'])) {
            $api_key =  trim($_GET['api_key']);
            $is_new = false;
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://merchants.bloompay.co.uk/merchant/new_api_key");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $api_key = curl_exec($ch);
            curl_close($ch);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://merchants.bloompay.co.uk/merchant/{$api_key}/export_wallet_info");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $wallet = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $mnemonic = $wallet['mnemonic'];
        $merchant_address = $wallet['merchant_address']['address'];
    ?>
    <div class="container my-4">
        <h1 class="mb-5">Bloompay USDS Gateway Installation Guide<!--/h1-->
        <div class="pagelayer-divider-holder">
	    <span class="pagelayer-divider-seperator"></span>
        </div></h1>

        <div class="alert alert-warning" role="alert">
            <i class="glyphicon glyphicon-hourglass"></i>
            <?php if ($is_new) echo 'New Bloompay merchant wallet and API key have been generated. <br />'; ?>API key: <b><?= $api_key ?></b>
            </i>
        </div>
        <br />
        USDS payments from your e-commerce store will be automatically sent to your merchant wallet which is linked to the assigned API key. 
        <br /><br /><br />

        <!-- Section 1: Importing HD Wallet -->
        <!--
        <div class="mb-5">
            <h2>Step 1: Importing your Merchant Wallet into TrustWallet</h2>

        <div class="alert alert-info" role="alert">
            <i class="glyphicon glyphicon-hourglass"></i>
            You can use any wallet app that can hold USDS BEP-20 tokens to access your merchant wallet, in this tutorial well use TrustWallet.
            </i>
        </div>


            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.1 Open TrustWallet App</h5>
                    <p>Go to 'Settings', then 'Wallets', click on the plus ('+') button and select 'I already have a Wallet'.</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.2 Select Multi-Coin Wallet</h5>
                    <p>On the next screen, select the 'Multi-Coin Wallet' option.</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.3 Insert Mnemonic Phrase</h5>
                    <p>Next, input your Mnemonic Phrase. Either scan the QR code with the top-right button or type in the 24-word phrase below.</p>
                    <div class="text-center">
                        <img src="https://merchants.bloompay.co.uk/merchant/<?= $api_key ?>/export_wallet_qr" alt="QR Code" class="img-fluid mb-2">
                        <p class="card-text highlight"><?= $mnemonic ?></p>
                    </div><br />
                    <p>Double check that scanned mnemonic is the same as the shown above and click 'Import' button.</span></p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.4 Add Custom Coin</h5>
                    <p>On the bottom of the main screen click 'Add Tokens'. Click on the plus ('+') button. Select <span class="highlight" data-no-copy>BNB Smart Chain</span> as 'Network'
                       and scan this QR code or input following to the 'Contract Address':</p>
                    <div class="text-center">
                        <img src="contract_qr.png" alt="QR Code" class="img-fluid mb-2">
                        <p class="card-text highlight"><?= $contract ?></p>
                    </div><br />
                    <p>You should now see <span class="highlight" data-no-copy>USDSHARES</span> as in the 'Name' field. Finally, press 'Save'.</p>
                </div>
            </div>
        </div>
        -->
        <!-- Section 1: Installing Bloompay USDS Gateway Plugin -->
        <div>
            <h2>Step 1: Installing the Bloompay USDS Gateway Plugin</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.1 Download and Install the Plugin</h5>
                    <p>
                        <div class="text-center">
                            <a href="bloompay.zip" id="backupButton" class="btn btn-primary mt-3" Xdata-loading="Backing up" target="_blank">
                                Download Plugin
                            </a><br /><br />
                        </div>
                        Download the plugin and install it from <i>WooCommerce
                       &raquo; Plugins</i> menu, click <i>Add new</i> button on the top, then click to <i>Upload plugin</i>, upload the file and click Install, then Activate.</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.2 Enter API Key</h5>
                    <p>Write down the following API Key we have just generated for you:</p>
                    <div class="text-center">
                        <p class="card-text highlight"><?= $api_key ?></p>
                    </div>
                    <br />
                    <p>It will be required to access your Dashboard. Navigate to the settings of the Bloompay USDS Gateway plugin by clicking on the <i>WooCommerce
                       &raquo; Settings</i> in the menu, open <i>Payments tab</i>, click to <i>All payment methods</i>, find Bloompay USDS Gateway and click <i>Manage</i>. Fill it into the 'API Key' field and press save.
                    </p>
                </div>
            </div>
            <!--
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">1.3 Complete the configuration</h5>
                    <p>Fill in the 'Service URL' field with the following value:</p>
                    <div class="text-center">
                        <p class="card-text highlight"><?= $svc_url ?></p>
                    </div><br />
                    <p>Review Title and Description fields which you can customize as required.</p>
                </div>
            </div>
            -->
        </div>

        <br />

        <!-- Section 2: Sending gas -->
        <div>
            <h2>Step 2: Top-up BNB for gas</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">2.1 Send BNB</h5>
                    <p>For automatic transfer of payments to your merchant wallet to work you will need send BNB to be used for gas. A balance of least 0.01 BNB is recommended.
                    </p>
                    <div class="text-center">
                        <img src="https://merchants.bloompay.co.uk/merchant/<?= $api_key ?>/wallet_address_qr" alt="QR Code" class="img-fluid mb-2">
                        <p class="card-text highlight"><?= $merchant_address ?></p>
                    </div><br />
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">2.2 Log in to your Merchant Dashboard</h5>
                    <p>Now you can visit your merchant dashboard page to see transaction summary and other information. Use your
                        secret API key to log in to the Dashboard in the future.
                    <div class="text-center">
                        <a href="https://merchants.bloompay.co.uk/tx.php?api_key=<?= $api_key ?>" id="backupButton" class="btn btn-primary mt-3" Xdata-loading="Backing up" target="_blank">
                            Open Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <br />

        Now you are ready to receive payments in USDS.

        <br /><br />
<!--
        <div class="alert alert-danger" role="alert">
            <i class="glyphicon glyphicon-hourglass"></i>
            To prevent loss of funds make sure you backup your new merchant wallet. This file will also include your API key backup and mnemonic phrase.
            <div class="text-center">
                <button id="backupButton" class="btn btn-primary mt-3" Xdata-loading="Backing up" data-toggle="modal" data-target="#backupWalletModal">
                    Backup Wallet
                </button>
            </div>
        </div>
-->
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
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

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


    </script>


</body>
</html>
