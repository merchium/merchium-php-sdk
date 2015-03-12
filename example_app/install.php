<?php

require '../MerchiumClient.php';

//define('MERCHIUM_DEBUG', true);

define('MERCHIUM_APP_KEY', '[YOUR_APP_KEY]');
define('MERCHIUM_CLIENT_SECRET', '[YOUR_CLIENT_SECRET]');

if (!empty($_GET['code'])) {
    $shop_domain = $_GET['shop_domain'];
    $merchium = new MerchiumClient(MERCHIUM_APP_KEY, MERCHIUM_CLIENT_SECRET, $shop_domain);

    if ($merchium->validateSignature($_GET) != true) {
        echo "<p>Error validate signature</p>";
        exit;
    }

    $access_token = $merchium->requestAccessToken($_GET['code']);
    if (empty($access_token)) {
        echo "<p>Error raised: " . $merchium->getLastError() . "</p>";
        exit;
    }

    header("P3P: CP=\"Empty P3P policy\"");//IE third party cookie hack

    setcookie('merchium_shop_domain', $shop_domain, time() + 60 * 60 * 24 * 30, '/');
    setcookie('merchium_access_token', $access_token, time() + 60 * 60 * 24 * 30, '/');

    //
    // Redirect
    //
    header('Location: app.php');
    exit;
}

?>
