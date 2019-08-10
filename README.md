# News Aggregator for IMT

## Requirements

* PHP `>7`
* composer

Collection of aggregators that render to HTML output. 

`sites.json` contains information on websites which the scripts will reference when pulling data. 

Copy over the example `db/example.*.json` files and run `php cron.php` a few times to aggregate sites. It's best to run `cron.php` on a 5~ minute timer.

### Composer

Composer is a PHP package manager. It supports autoloading classes according to psr-4 (how the code in `/src` is organized).

It also supports automatic downloading and autoloading of code hosted on [https://packagist.org](https://packagist.org). We use it to include [PHPMailer](https://packagist.org/packages/phpmailer/phpmailer), for sending mail.

## License

Code in `/src` is licensed under the GPL.

## Todo

* Maybe a table of contents at top of homepage, with anchors to sections. Fixed on mobile?
* Socialist appeal is only returning five articles, see if there's a way to get more (all joomla ones only send five .. :/)
* Make the jsondatabaseprocessor read {'keys' => [...], 'data' => [...]} (default null basically. Is a bit tough...)
* Customizations for home page
* Write out the mailing list / notifications code
* Separate `/src` into its own composer-optimized package (and repo?) (called Marxist.social News Aggregator). Then just have this repo reference it.
* Translation for static text
* Move CSS into `/src`
* Add table of contents w/ anchor links based on list of sites
