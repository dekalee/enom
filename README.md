Dekalee Enom
============

[![Build Status](https://travis-ci.org/dekalee/enom.svg?branch=master)](https://travis-ci.org/dekalee/enom)
[![Latest Stable Version](https://poser.pugx.org/dekalee/enom/v/stable)](https://packagist.org/packages/dekalee/enom)
[![Total Downloads](https://poser.pugx.org/dekalee/enom/downloads)](https://packagist.org/packages/dekalee/enom)
[![License](https://poser.pugx.org/dekalee/enom/license)](https://packagist.org/packages/dekalee/enom)

This php library will be an abstraction for the Enom API.

Usage
-----

We provide different classes to perform the requests :

- Get domain status
- Purchase domain
- Purchase additionnal services
- Set Dns host

Each of this query should be called with an argument which is the corresponding facade.

For exemple, to purchase the `test.com` domain:

```php

use GuzzleHttp\Client;
use Dekalee\Enom\PurchaseQuery;

$client = new Client();
$query = new PurchaseQuery('your-uid', 'your-password', $client, 'https://reseller.enom.com/interface.asp');

$facade = new PurchaseFacade();
$facade->sld = 'test';
$facade->tld = 'com';

$order = $query->execute($facade);

var_dump($order);
```
