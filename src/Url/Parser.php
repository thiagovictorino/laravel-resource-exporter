<?php

namespace Victorino\ResourceExporter\Url;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use Victorino\ResourceExporter\Exceptions\UrlParserException;
use function GuzzleHttp\Psr7\str;

/**
 * Class Parser
 * @package Victorino\ResourceExporter
 */
class Parser
{

  /**
   * @var $httpClient Client
   */
  protected $httpClient;

  /**
   * @var \Illuminate\Support\Collection
   */
  protected $result;

  /**
   * @var $builder Builder
   */
  protected $builder;

  /**
   * The current URL requested
   * @var $url string
   */
  protected $url;

  /**
   * Parser constructor.
   * @param Client $client
   */
  public function __construct(Client $client)
  {
    $this->httpClient = $client;
    $this->result = collect();
  }


  /**
   * Makes the request itself and create the results
   * @param Builder $builder
   * @return iterable
   * @throws UrlParserException
   */
  public function load(Builder $builder): iterable
  {
    $this->builder = $builder;
    $this->url = $this->builder->getEndpoint();
    $this->validateBeforeLoad();
    $this->sendRequest();
    return $this->result;
  }

  /**
   * Send the request to the endpoint
   * @throws UrlParserException
   */
  protected function sendRequest()
  {
    try {

      $response = $this->httpClient->request('GET', $this->url, [
        'headers' => $this->setHeaders()
      ]);

      $next_url = $this->setResult($response->getBody()->getContents());

      if (!empty($next_url)) {
        $this->url = $next_url;
        sleep($this->builder->getDelay());
        $this->sendRequest();
      }

    } catch (RequestException $e) {

      if ($e->hasResponse()) {
        throw new UrlParserException($e->getMessage() . ' - Response:' . str($e->getResponse()), $e->getCode(), $e);
      }

      throw new UrlParserException($e->getMessage() . ' - Request:' . str($e->getRequest()), $e->getCode(), $e);

    } catch (\Exception $e) {

      throw new UrlParserException($e->getMessage(), $e->getCode(), $e);

    }

  }

  /**
   * Define the header to be added on request
   * @return array
   */
  protected function setHeaders()
  {
    return array_merge(
      [
        'Accept' => 'application/json'
      ],
      $this->setAuthHeaders()
    );
  }

  /**
   * Define the header if an authentication is needed
   * @return array
   */
  protected function setAuthHeaders(): array
  {

    if (!empty($this->bearerToken)) {
      return ['Authorization' => 'Bearer ' . $this->builder->getBearerToken()];
    }

    return [];
  }

  /**
   * Validate things before request
   * @throws UrlParserException
   */
  private function validateBeforeLoad()
  {
    if (empty($this->url)) {
      throw new UrlParserException('You must inform an URL at least before call load()');
    }
  }

  /**
   * Handle the response from the request
   * @param string $contents
   * @return string|null
   * @throws UrlParserException
   */
  private function setResult(string $contents): ?string
  {
    $json = json_decode($contents);

    if ($this->builder->getPayload() == PayloadType::BOOTSTRAP3) {
      return $this->parseBootstrapThreeResult($json);
    }

    if ($this->builder->getPayload() == PayloadType::DEFAULT) {
      return $this->parseDefaultResult($json);
    }

    throw new UrlParserException('The payload is not valid: ' . $this->builder->getPayload());
  }

  /**
   * Parse the default payload result
   * @param $json
   * @return string
   */
  protected function parseDefaultResult($json)
  {

    $this->result->push($json->data);

    if (empty($json->next_page_url)) {
      return '';
    }
    return $json->next_page_url . '&' . urldecode(http_build_query($this->builder->getRequest()->except('page')));
  }

  /**
   * Handle the result from request when it is a
   * bootstrap 3 content
   * @param $json
   * @return string
   * @throws UrlParserException
   */
  private function parseBootstrapThreeResult($json)
  {

    if (!isset($json->meta)) {
      throw new UrlParserException('This is not a Bootstrap 3 format');
    }
    $this->result->push($json->data);

    if (empty($json->meta->pagination->links->next)) {
      return '';
    }

    return $json->meta->pagination->links->next . '&' . urldecode(http_build_query($this->builder->getRequest()->except('page')));
  }
}
