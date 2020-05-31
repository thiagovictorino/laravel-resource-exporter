<?php

namespace Victorino\ResourceExporter\Tests;

use Illuminate\Database\Eloquent\Factory;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Victorino\ResourceExporter\ResourceExporterServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->setUpDataBase();

        $this->app->make(Factory::class)->load(__DIR__.'/factories');
    }

    /**
     * Get package providers.
     *
     * @param \Illuminate\Foundation\Application $app
     *
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ResourceExporterServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ResourceExporter' => \Victorino\ResourceExporter\Facades\ResourceExporter::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:',
        ]);
        $app['config']->set('filesystems.disks');
        $app['config']->set('filesystems.disks.local', [
            'driver' => 'local',
            'root' => storage_path('path'),
        ]);

        $app['config']->set('resource-exporter.disk', 'local');
        $app['config']->set('resource-exporter.payload', 'default');
    }

    protected function setUpDataBase()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }
}
