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
  protected $builder;

  /**
   * ResourceExporter constructor.
   * @param Builder $builder
   */
  public function __construct(Builder $builder)
  {
    $this->builder = $builder;
  }

  /**
   * Set the endpoint where the data will be get
   * @param string $endpoint
   * @return Builder
   * @throws Exceptions\UrlParserException
   */
  public function endpoint(string $endpoint): Builder
  {
    return $this->builder->setEndpoint($endpoint);
  }
}