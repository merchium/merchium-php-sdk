<?php

require 'MerchiumClient.php';

//define('MERCHIUM_DEBUG', true);

define('MERCHIUM_APP_KEY', '');
define('MERCHIUM_SHARED_SECRET', '');
define('MERCHIUM_SHOP_DOMAIN', '');
define('MERCHIUM_PERMISSIONS_SCOPE', 'all');


$shop_domain = empty($_COOKIE['merchium_shop'])? MERCHIUM_SHOP_DOMAIN : $_COOKIE['merchium_shop'];
$access_token = empty($_COOKIE['merchium_access_token'])? '' : $_COOKIE['merchium_access_token'];


$merchium = new MerchiumClient(MERCHIUM_APP_KEY, MERCHIUM_SHARED_SECRET, $shop_domain, $access_token);

if (empty($_GET['code']) && empty($access_token)) {
//
// Direct installation
//
$url = $merchium->getInstallationUrl(MERCHIUM_PERMISSIONS_SCOPE);

echo "Click to <a href='{$url}>Install example application<a> to shop at {$merchium->shop_domain}";


} elseif (!empty($_GET['code'])) {
    if ($merchium->validateSignature($_GET) != true) {
        echo "<p>Error validate signature</p>";
        exit;
    }

    $shop_domain  = $_GET['shop'];
    $access_token = $merchium->requestAccessToken($_GET['code']);
    if (empty($access_token)) {
        echo "<p>Error raised: " . $merchium->getLastError() . "</p>";
        exit;
    }

    setcookie('merchium_shop', $shop_domain, time() + 60*60*24*30, '/');
    setcookie('merchium_access_token', $access_token, time() + 60*60*24*30, '/');

    //
    // Redirect from embed
    //
    header('Location: example.php');
    exit;

} elseif (!empty($access_token)) {
?>
<!DOCTYPE html>
<html>
<head>
<script src='https://<?php echo $shop_domain ?>/js/addons/applications/app.js'></script>
<script>
    MerchiumApp.init({
        appKey: '<?php echo  MERCHIUM_APP_KEY ?>',
        shopDomain: '<?php echo $shop_domain ?>'
    });

    MerchiumApp.ready(function()
    {
        MerchiumApp.Bar.configure({
            title: 'New Title',
            buttons: {
                pagination: { type: 'group', items: {
                                prev: {label: '', href: 'http://example.com/?step=prev', target: 'app', icon: 'icon-chevron-left'},
                                next: {label: '', href: 'http://example.com/?step=next', target: 'app', icon: 'icon-chevron-right'},
                            }
                },

                new:     { label: 'new',   target: 'new',   href: 'http://example.com/'},
                self:    { label: 'self',  target: 'self',  href: 'http://example.com/'},
                admin:   { label: 'admin', target: 'admin', href: 'admin.php?dispatch=categories.manage'},
                app:     { label: 'app',   target: 'app',   href: 'http://example.com/'},

                more: { label: 'More', type: 'dropdown', icon: 'icon-list-alt', items: {
                                reload:   { label: 'Reload', href: window.location.href, target: 'app', icon: 'icon-repeat'},
                                print:    { label: 'Print',  callback: function() { MerchiumApp.print(); }},
                                alert:    { label: 'Alert',  callback: function() { alert('alert!'); }},
                                divider:  { type: 'divider' },
                                showLoading:   {label: 'Show Loading', callback: function() { MerchiumApp.showLoading(); }},
                                hideLoading:   {label: 'Hide Loading', callback: function() { MerchiumApp.hideLoading(); }},
                                divider2: { type: 'divider' },
                                notice:   { label: 'Show Notice',  callback: function() { MerchiumApp.showNotice('Notice',  'Message', true); }},
                                warning:  { label: 'Show Warning', callback: function() { MerchiumApp.showWarning('Warning', 'Warning message', true); }},
                                error:    { label: 'Show Error',   callback: function() { MerchiumApp.showError('Error',  'Error message', true); }}
                            }/* items */
                }/* more */
            }
        });

        //MerchiumApp.Bar.remove('divider2');
        //MerchiumApp.Bar.remove('app');
        //MerchiumApp.Bar.remove(['new', 'prev']);
    });
</script>
</head>
<body>
<?php
    echo "<p>ADMIN AREA</p>";
    echo "<pre>";
    echo "<p>Token: {$access_token}<br> Shop: {$shop_domain}</p>";
    echo "<p>Lets send some test requests:</p>";

    //
    // Send Create
    //
    $res = $merchium->createRequest('products', array(
        'product_code'  => 'test',
        'product'       => 'Test product',
        'price'         => '25.0',
        'company_id'    => 1,
        'status'        => 'A',
        'main_category' => '166',
        'category_ids'  => array('166'),
    ));

    if ($res === false) {
        echo "<p>Error raised on create request: <b>" . $merchium->getLastError() . "</b></p>";
        exit;
    }

    $new_product_id = $res['product_id'];
    echo "<p>Product with product_id={$new_product_id} was successfully created.</p>";


    //
    // Send Update
    //
    $res = $merchium->updateRequest("products/{$new_product_id}", array('price' => 2500.0));
    if ($res === false) {
        echo "<p>Error raised on update request: <b>" . $merchium->getLastError() . "</b></p>";
        exit;
    }

    echo "<p>Price for product with product_id={$res['product_id']} was successfully updated.</p>";


    //
    // Send
    //
    $gres = $merchium->getRequest('products', array('q' => 'Test product', 'pname' => 'Y'));
    if ($gres === false) {
        echo "<p>Error raised on get request: <b>" . $merchium->getLastError() . "</b></p>";
        exit;
    }

    $product_id = $gres['products'][0]['product_id'];
    echo "<p>Product with product_id={$product_id} was successfully found.</p>";


    //
    // Send Delete
    //
   foreach ($gres['products'] as $r) {
        $product_id = $r['product_id'];
        $res = $merchium->deleteRequest("products/{$product_id}", array('price' => 2500.0));
        if ($res == false) {
            echo "<p>Error raised on delete request: <b>" . $merchium->getLastError() . "</b></p>";
            exit;
        }
        echo "<p>Product with product_id={$product_id} was successfully deleted.</p>";
    }

}

?>