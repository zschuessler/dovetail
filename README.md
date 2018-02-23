# A Teamwork.com API for Laravel 5

Access your Teamwork.com data in an easy, fluent, API for Laravel 5.

Look at all the fun you can have!

![code sample](https://squarebit.io/storage/products/16/Rwd9Kh2XZcYwuqqnyn8ueY7fPsxNwljMscfCYL3b.png)

Still not sure? Check out the 
[API cheat sheet](https://squarebit.io/zschuessler/dovetail/documentation/getting-started/api-request-cheat-sheet)
for a look at how you'll interact with the API.

# Quickstart Guide

**Install**

```
composer require squarebit/dovetail
```

If you aren't on Laraqve 5.5+, you must manually add the service provider to your `app/config.php` file:

```php
/**
 * Custom Service Providers
 */
SquareBit\Dovetail\ServiceProvider::class,
```

**Configure API Settings**

```php
php artisan vendor:publish --provider="SquareBit\Dovetail\ServiceProvider"
```

You can  now set your default API key and Teamwork.com domain in `config/dovetail.php`.

Need a key? See the Teamwork.com docs: [Getting your API Key](https://developer.teamwork.com/introduction#so_how_do_you_get)

**Usage**

Let's get your latest account activity, shall we?

```php
<?php
$dovetail = new \SquareBit\Dovetail\Dovetail;
    
$allActivity = $dovetail->activity()->all();
```

Want to change who you are authenticated as? That's easy too. You can either set the config programmatically with methods,
or pass in an ApiClient object:

```php
<?php

// Set all at once!
$dovetail = new \SquareBit\Dovetail\Dovetail(
    new \SquareBit\Dovetail\Api\Client('my-api-key', 'https://myDomain.teamwork.com')
);
$allActivity = $dovetail->activity()->all();

// Or with a method...
$dovetail = new \SquareBit\Dovetail\Dovetail;
$dovetail->apiClient->setApiKey('my-new-key');
$dovetail->apiClient->setApiUrl('https://myDomain.teamwork.com');

$allActivity = $dovetail->activity()->all();

```

**Full API Cheat Sheet**

Thirsty for more? Check out the full API cheat sheet:

https://squarebit.io/zschuessler/dovetail/documentation/getting-started/api-request-cheat-sheet

Want to see the official Quickstart Guide? It's here:

https://squarebit.io/zschuessler/dovetail/documentation/getting-started/quickstart-guide

# Roadmap

The following endpoints will be added before 02/28/2018:

* boards
* categories
* calendar events
* files
* time tracking

For business users, full webhook support will be available 03/01/2018. You'll get full ability to consume and respond
to Teamwork.com events as they happen - woohoo! You can get an unlimited usage license on the 
[SquareBit.io Dovetail page.](https://squarebit.io/zschuessler/dovetail)

# Unit Tests

This package has over 75+ unit tests and growing. If interested please see the business license on SquareBit.io.

# License

If you are a business or intending on commercial use, please pay for a license: 
[Dovetail on SquareBit.io](https://squarebit.io/zschuessler/dovetail).

If you intend on using this repository without commercial use, the code is licensed under
[Creative Commons Attribution NonCommercial \(CC-BY-NC\)](https://tldrlegal.com/license/creative-commons-attribution-noncommercial-\(cc-nc\)).


