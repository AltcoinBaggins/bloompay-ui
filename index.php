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
        $svc_url = 'https://bloompay.bloomshares.com:48080';
        $self_url = "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
        $is_new = true;

        if (isset($_GET['api_key']) && !empty($_GET['api_key'])) {
            $api_key =  trim($_GET['api_key']);
            $is_new = false;
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://bloompay.bloomshares.com:48080/merchant/new_api_key");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $api_key = curl_exec($ch);
            curl_close($ch);
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://bloompay.bloomshares.com:48080/merchant/{$api_key}/export_wallet");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $wallet = json_decode(curl_exec($ch), true);
        curl_close($ch);

        $mnemonic = $wallet['mnemonic'];
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
        USDS payments from your e-commerce store will be directed right to your merchant HD wallet which is linked to the assigned API key. 
        <br /><br /><br />

        <!-- Section 1: Importing HD Wallet -->
        <div class="mb-5">
            <h2>Step 1: Importing your HD Wallet into TrustWallet</h2>

        <div class="alert alert-info" role="alert">
            <i class="glyphicon glyphicon-hourglass"></i>
            You can use any modern HD wallet witch supports BIP-0044 specification and BEP-20 tokens, in this example we will use TrustWallet.
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
                        <img src="https://bloompay.bloomshares.com:48080/merchant/<?= $api_key ?>/export_wallet_qr" alt="QR Code" class="img-fluid mb-2">
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

        <!-- Section 2: Installing BloompayGateway Plugin -->
        <div>
            <h2>Step 2: Installing the BloompayGateway Plugin</h2>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">2.1 Install Plugin</h5>
                    <p>Install the BloompayGateway plugin in your WooCommerce setup. After installation, navigate to the settings of the BloompayGateway plugin by clicking on the <i>WooCommerce
                       &raquo; Settings</i> in the menu, open <i>Payments tab</i>, click to <i>All payment methods</i>, find BloompayGateway and click <i>Manage</i>.</p>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">2.2 Enter API Key</h5>
                    <p>Enter the following key into the 'API Key' field:</p>
                    <div class="text-center">
                        <p class="card-text highlight"><?= $api_key ?></p>
                    </div>
                </div>
            </div>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title">2.3 Complete the configuration</h5>
                    <p>Fill in the 'Service URL' field with the following value:</p>
                    <div class="text-center">
                        <p class="card-text highlight"><?= $svc_url ?></p>
                    </div><br />
                    <p>Review Title and Description fields which you can customize as required.</p>
                </div>
            </div>
        </div>

        <br />

        Now you are ready to receive payments in USDS. Payments from your ecommerce store linked to the generated API key
        will appear automatically in your TrustWallet.

        <br /><br /><br />

        <div class="alert alert-danger" role="alert">
            <i class="glyphicon glyphicon-hourglass"></i>
            To prevent loss of funds make sure you write down your mnemonic phrase. You can also save direct link to this
            exact page for your specific API key to easily import the same wallet in the future:<br />
            <span class="highlight"><?= $self_url ?>?api_key=<?= $api_key ?></span>
            </i>
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
    </script>


</body>
</html>
