<?php

namespace Victorino\ResourceExporter\Facades;

use Illuminate\Support\Facades\Facade;

class ResourceExporter extends Facade
{
  protected static function getFacadeAccessor()
  {
    return \Victorino\ResourceExporter\ResourceExporter::class;
  }

}