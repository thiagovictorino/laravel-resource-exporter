## Usage

You can now add messages using the Facade (when added), using the PSR-3 levels (debug, info, notice, warning, error, critical, alert, emergency):

```php

use  \Victorino\ResourceExporter\ResourceExporter;
/**
 * Getting the export builder 
 */
$exporter =ResourceExporter::endpoint('http://you-url.com?anyfilters');

/**
 * optional: Set the Bearer Token on request
 */
$exporter->withBearerToken('abcd123');

/**
 * optional: Set the payload as a Bootstrap 3 standard. You can set it automatically
 * on configurations
 */ 
$exporter->withBootstrapThree();

//optional: Add a delay between each requests
$exporter->withDelay(5);

/**
 * Will save the file on disk that was set on configurations 
 */
$exporter->toCSV('my-file-name');
```