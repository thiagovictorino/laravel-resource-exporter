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

    public function register()
    {

    }

    private function registerResources()
    {

    }
}