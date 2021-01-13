# PHP Facebook Scraper

[![Latest Version on Packagist](https://img.shields.io/packagist/v/piedweb/FacebookScraper.svg?style=flat-square)](https://packagist.org/packages/piedweb/FacebookScraper)
[![GitHub Tests Action Status](https://img.shields.io/github/workflow/status/piedweb/FacebookScraper/Tests?label=tests)](https://github.com/PiedWeb/FacebookScraper/actions)
[![Total Downloads](https://img.shields.io/packagist/dt/piedweb/facebook-Scraper.svg?style=flat-square)](https://packagist.org/packages/piedweb/facebook-Scraper)

{ Harvest Data | Scrap } from Scrape Facebook public pages without an API key.

_DISCLAMER_ : this code is share only for educational purpose. Using it _probably_ against facebook's terms of service.

## Installation

```bash
composer require piedweb/facebook-scraper
```

## Usage

```php
use PiedWeb\FacebookScraper\FacebookScraper;

$fbScraper = new FacebookScraper('myPageId');

$fbScraper->getPosts();

/** @Return array with
 * publish_time
 * post_id
 * text
 * comment_number
 * like_number
 * images array
 */
```

## Testing

```bash
composer test
```

## Todo

-   review
-   post comment

## Credits

-   Robin from [Pied Web](https://piedweb.com)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

<p align="center"><a href="https://dev.piedweb.com" rel="dofollow">
<img src="https://raw.githubusercontent.com/PiedWeb/piedweb-devoluix-theme/master/src/img/logo_title.png" width="200" height="200" alt="PHP Packages Open Source" />
</a></p>
