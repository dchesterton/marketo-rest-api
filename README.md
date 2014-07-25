Marketo REST API Client
================
Unofficial PHP client for the Marketo.com REST API: http://developers.marketo.com/documentation/rest/. Requires PHP 5.4+

Installation
----------------
The recommended way of installing the client is via [Composer](http://getcomposer.org/). Simply add the following line
to the 'require' section of your composer.json file and run `composer update`.

    "dchesterton/marketo-rest-api": "dev-master"

Setup
----------------
The client is built on [Guzzle 3](http://guzzle3.readthedocs.org) and uses a factory method to create an instance. e.g.

```php
use CSD\Marketo\Client;

$client = Client::factory(array(
    'client_id' => 'Marketo client ID',
    'client_secret' => 'Marketo client secret',
    'munchkin_id' => '100-AEK-913'
));
```

Client ID and secret are required. You must also specify either a Munchkin Code or the full url, e.g. 

```php
$client = Client::factory(array(
    'client_id' => 'Marketo client ID',
    'client_secret' => 'Marketo client secret',
    'url' => 'https://100-AEK-913.mktorest.com'
));
```

Usage
----------------
View the source of `src/Client.php` for all the available methods.

License
----------------
This source is licensed under an MIT License, see the LICENSE file for full details. If you use this code, it would be great to hear from you.
