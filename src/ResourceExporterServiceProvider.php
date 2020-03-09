<?php

namespace thiagovictorino\ResourceExporter;


use Illuminate\Support\ServiceProvider;

/**
 * Class ResourceExporterServiceProvider
 *
 * @package thiagovictorino\ResourceExporter
 */
class ResourceExporterServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->registerResources();
    }

    private function registerResources()
    {
      $this->publishes([
        __DIR__ . '../config/resource-exporter.php' => config_path('resource-exporter.php'),
      ]);
    }
}