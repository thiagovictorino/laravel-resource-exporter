<?php


namespace thiagovictorino\ResourceExporter\Tests;


use thiagovictorino\ResourceExporter\ResourceExporterServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
          ResourceExporterServiceProvider::class,
        ];
    }

}