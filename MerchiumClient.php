<?php

class MerchiumClient
{
    const LIB_VERSION = '0.9.7';

    public $shop_domain;

    protected $token;
    protected $app_key;
    protected $access_token;
    protected $client_secret;
    protected $last_response_headers = null;
    protected $last_error_status = 0;
    protected $last_error = '';

    public function __construct($app_key, $client_secret, $shop_domain = '', $access_token = '')
    {
        $this->app_key       = $app_key;
        $this->client_secret = $client_secret;
        $this->shop_domain   = rtrim($shop_domain, '/') . '/';
        $this->access_token  = $access_token;
    }

    public function setAccessToken($access_token)
    {
        $this->access_token = $access_token;
    }

    public function setShopDomain($shop_domain)
    {
        $this->shop_domain = rtrim($shop_domain, '/') . '/';
    }

    public function getInstallationUrl($scope, $redirect_uri = '')
    {
        $url = "http://{$this->shop_domain}api/authorize/?client_id={$this->app_key}&scope=" . $scope;
        if (!empty($redirect_uri)) {
            $url .= "&redirect_uri=" . urlencode($redirect_uri);
        }

        return $url;
    }

    public function requestAccessToken($code)
    {
        $url = "http://{$this->shop_domain}api/access_token";
        $params = array(
            'client_id'     => $this->app_key,
            'client_secret' => $this->client_secret,
            'code'          => $code,
        );

        $params = $this->jsonEncode($params);
        if ($params === false) {
            return false;
        }

        $response = $this->processRequest('POST', $url, $params);

        if (!empty($response) && isset($response['access_token'])) {
            return $response['access_token'];
        } else {
            return false;
        }
    }

    protected function httpRequest($method, $url, $params = '', $request_headers = array())
    {
        if (!function_exists('curl_init')) {
            $this->last_error = "Curl PHP module not found";
            return false;
        }

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 3);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);//Todo
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_USERAGENT, 'MerchiumClient '.self::LIB_VERSION);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);

        if (!empty($request_headers)) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
        }

        if (is_array($params)) {
            $params = http_build_query($params, '', '&');
        }

        if ($method == 'GET') {
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            $url = $url . "?" . $params;//TODO: carefully build query

        } elseif($method == 'POST') {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);

        } elseif($method == 'DELETE') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');

        } elseif($method == 'PUT') {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
        }

        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if (defined('MERCHIUM_DEBUG')) {
            var_dump($response);
        }

        if (!empty($error)) {
            $this->last_error = "Сurl error({$errno}): {$error}";
            return false;
        }

        while (strpos(ltrim($response), 'HTTP/1') === 0) {
            list($headers, $response) = preg_split("/(\r?\n){2}/", $response, 2);
        }

        if (empty($headers)) {
            $this->last_error = "Empty response";
            return false;
        }

        $this->last_response_headers = $headers;

        return array($headers, $response);
    }

    public function processRequest($method, $url, $params = '')
    {
        $request_headers = array();
        if (!empty($this->access_token)) {
            $request_headers[] = 'X-Merchium-Access-Token: ' . $this->access_token;
        }

        if ($method != 'GET') {
            $request_headers[] = 'Content-Type: application/json';
        }

        $res = $this->httpRequest($method, $url, $params, $request_headers);
        if ($res === false) {
            return false;
        }

        list($headers, $response) = $res;
        list(, $http_status_code, $http_status_message) = explode(' ', strtok(trim($headers), "\n"), 3);
        $http_status_message = trim($http_status_message);

        if ($http_status_code >= 300) {
            $this->last_error_status = $http_status_code;
            $this->last_error = $http_status_message;
        }

        $data = $this->jsonDecode($response);
        if ($data == false) {
            return false;
        }

        if (!empty($data) && !empty($data['status']) && $data['status'] >= 300) {
            $this->last_error_status = $data['status'];

            if (!empty($data['message'])) {
                $this->last_error = $data['message'];
            }

            return false;
        }

        return $data;
    }

    public function getRequest($path, $params = array())
    {
        $url = "http://{$this->shop_domain}api/" . trim($path, '/');
        return $this->processRequest('GET', $url, $params);
    }

    public function createRequest($path, $params)
    {
        $url = "http://{$this->shop_domain}api/" . trim($path, '/');
        $params = $this->jsonEncode($params);
        if ($params === false) {
            return false;
        }

        return $this->processRequest('POST', $url, $params);
    }

    public function updateRequest($path, $params)
    {
        $url = "http://{$this->shop_domain}api/" . trim($path, '/');
        $params = $this->jsonEncode($params);
        if ($params === false) {
            return false;
        }

        return $this->processRequest('PUT', $url, $params);
    }

    public function deleteRequest($path, $params = array())
    {
        $url = "http://{$this->shop_domain}api/" . trim($path, '/');
        $response = $this->processRequest('DELETE', $url, $params);       
        if (!empty($response) && isset($response['message']) && $response['message'] = 'Ok') {
            return true;
        } else {
            return false;
        }
    }

    public function testRequest()
    {
        $url = "http://{$this->shop_domain}api/test";
        $response = $this->processRequest('GET', $url);
        if (!empty($response) && isset($response['message']) && $response['message'] = 'Ok') {
            return true;
        } else {
            return false;
        }
    }

    public function validateSignature($get)
    {
        if (empty($get['signature'])) {
            return false;
        }

        $params = array();
        foreach($get as $name => $value) {
            if ($name == 'signature') {
                continue;
            }

            $params[] = $name . '=' . $value;
        }

        sort($params);

        $signature = md5($this->client_secret . join('', $params));

        return $get['signature'] === $signature;
    }

    protected function jsonEncode($data)
    {
        $encoded = json_encode($data);
        if ($encoded === false) {
            $this->last_error = "Could not JSON-encode the request data";
            return false;
        }

        return $encoded;
    }

    protected function jsonDecode($encoded)
    {
        $data = json_decode($encoded, true);
        if (is_null($data)) {
            if (empty($this->last_error)) {
                $this->last_error = "Could not JSON-decode the response";
            }
            return false;
        }

        return $data;
    }

    public function getLastErrorStatus()
    {
        return $this->last_error_status;
    }

    public function getLastError()
    {
        return $this->last_error;
    }
}

?>