Marketo REST API Client
================
Unofficial PHP client for the Marketo.com REST API: http://developers.marketo.com/documentation/rest/. Requires PHP 5.3.3+

Installation
----------------
The recommended way of installing the client is via [Composer](http://getcomposer.org/). Simply run the following command to add the library to your composer.json file.

    composer require dchesterton/marketo-rest-api

Setup
----------------
The client is built on [Guzzle 3](http://guzzle3.readthedocs.org) and uses a factory method to create an instance.
You must specify either a Munchkin ID or the full url.

####For Rest Api access:
```php
use CSD\Marketo\Client;

$client = Client::factory(array(
    'client_id' => 'Marketo client ID',         // required
    'client_secret' => 'Marketo client secret', // required
    'munchkin_id' => '100-AEK-913' // alternatively, you can supply the full URL, e.g. 'url' => 'https://100-AEK-913.mktorest.com'
));
```

####For Bulk Api access:
```php
use CSD\Marketo\Client;

$client = Client::factory(array(
    'client_id' => 'Marketo client ID',         // required
    'client_secret' => 'Marketo client secret', // required
    'munchkin_id' => '100-AEK-913' // alternatively, you can supply the full URL, e.g. 'url' => 'https://100-AEK-913.mktorest.com'
    'bulk' => true // if uploading leads via file upload (e.g. csv)
));
```

Usage
----------------
View the source of `src/Client.php` for all the available methods.

License
----------------
This source is licensed under an MIT License, see the LICENSE file for full details. If you use this code, it would be great to hear from you.
