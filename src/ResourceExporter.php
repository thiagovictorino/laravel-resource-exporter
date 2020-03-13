<?php

namespace Victorino\ResourceExporter;


use Illuminate\Support\Facades\Storage;
use Victorino\ResourceExporter\Exporters\CommaSeparatedValues;
use Victorino\ResourceExporter\Url\Builder;
use Victorino\ResourceExporter\Url\Parser;
use Victorino\ResourceExporter\Url\PayloadType;

/**
 * Class ResourceExporter
 * @package Victorino\ResourceExporter
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
   * @param string $endpoint
   * @return Builder
   * @throws Exceptions\UrlParserException
   */
  public function endpoint(string $endpoint): Builder
  {
    return $this->builder->setEndpoint($endpoint);
  }
}