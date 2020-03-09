## Usage

```php
/**
 * Getting the export builder 
 */
$exporter = \thiagovictorino\ResourceExporter\ResourceExporter::endpoint('http://your-url.com?anyfilters');

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