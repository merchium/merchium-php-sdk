<?php

require '../MerchiumClient.php';

//define('MERCHIUM_DEBUG', true);

define('MERCHIUM_APP_KEY', '');
define('MERCHIUM_SHARED_SECRET', '');

if (!empty($_GET['code'])) {
    $shop_domain = $_GET['shop'];
    $merchium = new MerchiumClient(MERCHIUM_APP_KEY, MERCHIUM_SHARED_SECRET, $shop_domain);

    if ($merchium->validateSignature($_GET) != true) {
        echo "<p>Error validate signature</p>";
        exit;
    }

    $access_token = $merchium->requestAccessToken($_GET['code']);
    if (empty($access_token)) {
        echo "<p>Error raised: " . $merchium->getLastError() . "</p>";
        exit;
    }

    setcookie('merchium_shop', $shop_domain, time() + 60 * 60 * 24 * 30, '/');
    setcookie('merchium_access_token', $access_token, time() + 60 * 60 * 24 * 30, '/');

    //
    // Redirect
    //
    header('Location: admin.php');
    exit;
}

?>
