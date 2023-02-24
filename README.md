# Bundesliga Live Result Scraper

![PHP](https://img.shields.io/badge/php-%3E%3D8.1-%238892BF?style=plastic&logo=php)
![License](https://img.shields.io/badge/license-MIT-green?style=plastic)

A scraper for for Deutsche Fussball Bundesliga 1 & 2 live results.

### Attention!

I am not owner or maintainer of the website (https://bundesliga.com). This is only a scraper for live results.
Use at your own risk.

## Requirement

You need a working environment with php <= 8.1 and composer.

## Installation

```zsh
composer require thomasboom89/bundesliga-live-result-scraper 
```

## Usage

Create an instance of Bundesliga

```php
$httpClient     = new Client();
$requestFactory = new HttpFactory();
$bundesliga     = new Bundesliga($httpClient, $requestFactory);
```

Now you can use it to make a request

```php
// You can choose between Erste and Zweite Bundesliga
$results = $bundesliga->getResults(Bundesliga\LigaType::SecondBundesliga);
```

You will receive an array of result objects

```php
var_dump($results);
```

## License

Bundesliga Live Result Scraper
Copyright (C) 2023 ThomasBoom89. MIT license.

Bundesliga Live Result Scraper includes several third-party Open-Source libraries, which are licensed under their
own respective Open-Source licenses.

See `composer license` for complete list of depending libraries.
