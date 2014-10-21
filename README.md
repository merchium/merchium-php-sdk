# Merchium PHP SDK

Здравствуйте, и добро пожаловать в **Merchium PHP SDK** — набор инструментов для разработки приложений для [Мерчиума](http://www.merchium.ru) на PHP.

Merchium PHP SDK включает в себя PHP-библиотеку **MerchiumClient.php** и пример приложения, написанного с ее использованием (разбор примера вы найдете в [документации](http://docs.merchium.ru/apps)).

## MerchiumClient.php

Библиотека MerchiumClient.php служит для общения c [REST API Мерчиума](http://docs.merchium.ru/apps/api). Библиотека представляет собой класс MerchiumClient, предоставляющий методы работы с API.

Версия библиотеки хранится в константе `MerchiumClient::LIB_VERSION`.

### Методы класса MerchiumClient

1. `__construct($app_key, $shared_secret, $shop_domain = '', $access_token = '')` — Конструктор класса.
  - `$app_key` — параметр приложения App key (см. страницу приложения в панели партнера).
  - `$shared_secret` — параметр приложения Shared secret (см. [Регистрация учетной записи партнера и создание приложения](https://docs.google.com/a/cs-cart.com/document/- d/1pYS6ta0NzWd_JmxP8xbmjDI8aCppJ8Z5JaFzB5DaZTs/edit#heading=h.x1i5nwg3pnh1)).
  - `$shop_domain` — Уникальный домен магазина на mymerchium.ru (например, mystore.mymerchium.ru).
  - `$access_token` — Токен доступа к API.
1. `setAccessToken($shop_domain)` — Установка значения токена доступа в API.
  - `$shop_domain` — Уникальный домен магазина на mymerchium.ru (например, mystore.mymerchium.ru).
1. `setShopDomain($access_token)` - Установка значения домена магазина.
  - `$access_token` — Токен доступа к API.
1. `getInstallationUrl($scope, $redirect_uri = '')` — Получение ссылки для установки приложения (см. [Авторизация. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)). Обычно этот метод не нужен, т. к. Маркет генерирует ссылку автоматически.
  - `$scope` — Список идентификаторов доступа.
  - `$redirect_uri` — Адрес для редиректа после установки приложения.
1. `requestAccessToken($code)` — Отправка запроса на получение токена доступа.
  - `$code1 — Временный код, (см. [Авторизация. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)).
1. `getRequest($path, $params)` — Отправка запроса на получение данных обьектов.
  - `$path` — Адрес объекта, т. е. часть URL после http://STORE_NAME.mymerchium.ru/api/.
  - `$params` — Параметры запроса.
1. `createRequest($path, $params)` — Отправка запроса на создание объекта.
  - `$path` — Адрес объекта, т. е. часть URL после http://STORE_NAME.mymerchium.ru/api/.
  - `$params` — Параметры запроса.
1. `updateRequest($path, $params)` — Отправка запроса на обновление данных объекта.
  - `$path` — Адрес объекта, т. е. часть URL после http://STORE_NAME.mymerchium.ru/api/.
  - `$params` — Параметры запроса.
1. `deleteRequest($path)` — Отправка запроса на удаление объекта.
  - `$path` — Адрес объекта, т. е. часть URL после http://STORE_NAME.mymerchium.ru/api/.
1. `testRequest()` — Отправка тестового запроса, например для проверки соединения.
1. `validateSignature($get)` — Валидация сигнатуры входящего запроса (см. [Авторизация. AccessToken](https://docs.google.com/a/cs-cart.com/document/d/16O3sURFHbPlBDWz2cIOPWp8oNd9mKDAHCAXByxjfseg/edit)).
  - `$get` — Параметры запроса к скрипту, обычно массив `$_GET`.
1. `getLastErrorStatus()` — Узнать код последней ошибки (из ответа на API-запрос). См. [Коды ошибок](https://docs.google.com/a/cs-cart.com/document/d/1izDXBnCPkrkwt7wPTTq8R7cORQOXGHYOhZTIt66pc5k/edit).
1. `getLastError()` - Узнать текст последней ошибки (из ответа на API-запрос). См. [Коды ошибок](https://docs.google.com/a/cs-cart.com/document/d/1izDXBnCPkrkwt7wPTTq8R7cORQOXGHYOhZTIt66pc5k/edit).

### Ошибки, генерируемые библиотекой

- **Curl PHP module not found** — Не найдено PHP-расширение curl.
- **Сurl error(CODE): ERROR** — Ошибки, генерируемые расширением curl.
- **Could not JSON-encode the request data** — Ошибка при JSON-кодировании данных.
- **Could not JSON-decode the response** — Ошибка при декодированни полученных от сервера JSON-данных.
- **Empty response** — Получен пустой ответ от сервера.
