## Usage

You can now add messages using the Facade (when added), using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

```php
$export = new \thiagovictorino\ResourceExporter\ResourceExporter::endpoint('http://youurl.com');
$export->withBearerToken('abcd123');
```