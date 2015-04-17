# Merchium PHP SDK

Hello and welcome to **Merchium PHP SDK**, a [Merchium](http://www.merchium.com) app development toolkit for PHP developers.

Merchium PHP SDK consists of the **MerchiumClient.php** PHP library and an example app (see it described in the [docs](http://docs.merchium.com/apps)).

## MerchiumClient.php

Use the MerchiumClient.php library to interact with the [Merchium REST API](http://docs.merchium.ru/apps/api). The library contains a single class MerchiumClient which offers methods for API interaction.

You can find the current library version in the `MerchiumClient::LIB_VERSION` constant.

### The MerchiumClient Class Methods

1. `__construct($app_key, $shared_secret, $shop_domain = '', $access_token = '')`—class constructor.
  - `$app_key`—the App key app param (see the app page in your Merchium partner panel).
  - `$shared_secret`—the Client secret app param (see [Регистрация учетной записи партнера и создание приложения](https://docs.google.com/document/d/1mU7cJTNlXuaiGIQ645gxu8XonV0xm7sGnKsjdJESxxs/edit#heading=h.92nl0c1q6xrh)).
  - `$shop_domain`—unique store domain at mymerchium.com (e.g. mystore.mymerchium.com).
  - `$access_token`—API access token.

1. `setAccessToken($shop_domain)`—set the API access token value.
  - `$shop_domain`—unique store domain at mymerchium.com (e.g. mystore.mymerchium.com).

1. `setShopDomain($access_token)`—set the store domain value.
  - `$access_token`—API access token.

1. `getInstallationUrl($scope, $redirect_uri = '')`—get the app installation link (see [Authorization. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)). Normally, you don't need to call this method manually, because the Marketplace generates the link itself.
  - `$scope`—permission scope list.
  - `$redirect_uri`—post-install redirect URI .

1. `requestAccessToken($code)`—send a request to get an API access token.
  - `$code`—temporary code, (see [Authorization. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)).

1. `getRequest($path, $params)`—send a request to get object data.
  - `$path`—path to the object, i.e. part of the URL after http://STORE_NAME.mymerchium.com/api/.
  - `$params`—request params.

1. `createRequest($path, $params)`—send a request to create an object.
  - `$path`—path to the object, i.e. part of the URL after http://STORE_NAME.mymerchium.com/api/.
  - `$params`—request params.

1. `updateRequest($path, $params)`—send a request to update object data.
  - `$path`—path to the object, i.e. part of the URL after http://STORE_NAME.mymerchium.com/api/.
  - `$params`—request params.

1. `deleteRequest($path)`—send a request to delete an object.
  - `$path`—path to the object, i.e. part of the URL after http://STORE_NAME.mymerchium.com/api/.

1. `testRequest()`—send a test request, e.g. to check the connection.

1. `validateSignature($get)`—validate the signature of an incoming request (see [Authorization. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)).
  - `$get`—request params, usually a `$_GET` array.

1. `getLastErrorStatus()`—get the last error code from the API response. See [Error Codes](https://docs.google.com/document/d/1wku883HFRjoaGsPK1Odkiu3DWJVjIkyhlaMrjxfJbw4/edit).

1. `getLastError()`—get the last error message from the API response. See [Error Codes](https://docs.google.com/document/d/1wku883HFRjoaGsPK1Odkiu3DWJVjIkyhlaMrjxfJbw4/edit).

### Errors Returned by the API

See [Error Codes](https://docs.google.com/document/d/1wku883HFRjoaGsPK1Odkiu3DWJVjIkyhlaMrjxfJbw4/edit).

### Errors Returned by the Library

- **Curl PHP module not found**—the curl PHP extension not found.
- **Сurl error(CODE): ERROR**—errors returned by the curl extension.
- **Could not JSON-encode the request data**—error during JSON encoding.
- **Could not JSON-decode the response**—error during deconding of the server returned JSON data.
- **Empty response**—empty response from the server.
