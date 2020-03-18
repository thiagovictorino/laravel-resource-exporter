Laravel Resource Exporter
=========================

This package helps you to export a data resource from an api 
that returns a Laravel pagination payload (including bootstrap 3 payload).

Currently it is exporting for CSV, but any PR is welcome =)

## Installation

Add package
```bash
composer require thiagovictorino/laravel-resource-exporter
``` 

### Configuration

To create configuration file run:

``` bash
php artisan vendor:publish --provider="Victorino\ResourceExporter\ResourceExporterServiceProvider"
```
## Usage
```php

/**
 * Getting the export builder 
 */

$exporter = \ResourceExporter::endpoint('http://you-url.com/resource?anyfilters');


/**
 * optional: Set the Bearer Token on request
 */
$exporter->withBearerToken('abcd123');

/**
 * optional: Set the payload as a Bootstrap 3 standard. You can set it automatically
 * on configurations
 */ 
$exporter->withBootstrapThree();

/**
 * optional: Add a delay between each requests
 */ 
$exporter->withDelay(5);

/**
 * Will save the file on disk that was set on configurations 
 */
$exporter->toCSV('my-file-name');
```

### Testing

``` bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

### Security

If you discover any security related issues, please email eu [at] thiagovictorino.com instead of using the issue tracker.

## Credits

- [Thiago Victorino](https://github.com/thiagovictorino)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Laravel Package Boilerplate

This package was generated using the [Laravel Package Boilerplate](https://laravelpackageboilerplate.com).
