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
   * @var Parser
   */
  protected $urlParser;

  /**
   * @var mixed
   */
  protected $fileContent;

  /**
   * Object that contains all request information
   * @var $builder Builder
   */
  protected $builder;

  /**
   * ResourceExporter constructor.
   * @param Parser $parser
   * @param Builder $builder
   */
  public function __construct(Parser $parser, Builder $builder)
  {
    $this->urlParser = $parser;
    $this->builder = $builder;
  }

  /**
   * Set the endpoint where the data will be get
   * @param string $endpoint
   * @return ResourceExporter
   * @throws Exceptions\UrlParserException
   */
  public function endpoint(string $endpoint): ResourceExporter
  {
    $this->builder->setEndpoint($endpoint);
    return $this;
  }

  /**
   * Set the bearer token as a header to request
   * @param string $token
   * @return ResourceExporter
   */
  public function withBearerToken(string $token): ResourceExporter
  {
    $this->builder->setBearerToken($token);
    return $this;
  }

  /**
   * Add a delay between each page request in seconds
   * @param int $seconds
   * @return ResourceExporter
   */
  public function withDelay(int $seconds): ResourceExporter
  {
    $this->builder->setDelay($seconds);
    return $this;
  }

  /**
   * Consider the content of request as a bootstrap 3 standard
   * @return $this
   */
  public function withBootstrapThree()
  {
    $this->builder->setPayload(PayloadType::BOOTSTRAP3);
    return $this;
  }

  /**
   * Export to CSV and save in file
   * @return string Name of the file saved
   * @throws Exceptions\UrlParserException
   */
  public function toCSV()
  {
    $result = $this->urlParser->load($this->builder);
    /**
     * @var $exporter CommaSeparatedValues
     */
    $exporter = resolve(CommaSeparatedValues::class);

    $this->fileContent = $exporter->export($result);
    $fileName = $this->generateRandomName() . '.csv';
    Storage::disk(config('resource-exporter.disk'))->put(
      $fileName,
      $this->fileContent);
    return $fileName;
  }

  /**
   * Generate a random name to file
   * @return string
   */
  protected function generateRandomName(): string
  {
    return md5(uniqid(rand(), true));
  }
}