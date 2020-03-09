<?php

namespace thiagovictorino\ResourceExporter;


use Illuminate\Support\Facades\Storage;
use thiagovictorino\ResourceExporter\Exporters\CommaSeparatedValues;
use thiagovictorino\ResourceExporter\Url\Builder;
use thiagovictorino\ResourceExporter\Url\Parser;
use thiagovictorino\ResourceExporter\Url\PayloadType;

/**
 * Class ResourceExporter
 * @package thiagovictorino\ResourceExporter
 */
class ResourceExporter
{

  /**
   * Object that contains all request information
   * @var $builder Builder
   */
  static $builder;

  /**
   * ResourceExporter constructor.
   * @param Builder $builder
   */
  public function __construct(Builder $builder)
  {
    self::$builder = $builder;
  }

  static function getBuilderInstance() {
    if (empty(self::$builder)) {
      self::$builder = resolve(Builder::class);
    }
    return self::$builder;
  }

  /**
   * @param string $endpoint
   * @return Builder
   * @throws Exceptions\UrlParserException
   */
  static function endpoint(string $endpoint): Builder
  {
    return self::getBuilderInstance()->setEndpoint($endpoint);
  }
}