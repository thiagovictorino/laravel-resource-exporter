<?php

namespace thiagovictorino\ResourceExporter;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use thiagovictorino\ResourceExporter\Exceptions\UrlParserException;
use function GuzzleHttp\Psr7\str;

/**
 * Class UrlParser
 * @package thiagovictorino\ResourceExporter
 */
class UrlParser
{
  /**
   * The endpoint of where the data will be get
   * @var $url string
   */
  protected $url;

  /**
   * @var $bearerToken string?
   */
  protected $bearerToken;

  /**
   * @var $httpClient Client
   */
  protected $httpClient;

  /**
   * @var int
   */
  protected $delay = 0;

  /**
   * @var bool
   */
  protected $bootstrapThree = false;

  /**
   * @var Request
   */
  protected $request;

  /**
   * @var \Illuminate\Support\Collection
   */
  protected $result;

  /**
   * UrlParser constructor.
   */
  public function __construct()
  {
    $this->httpClient = resolve(Client::class);
    $this->result = collect();
  }

  /**
   * @param string $url
   * @return UrlParser
   * @throws UrlParserException
   */
  public function endpoint(string $url): UrlParser
  {

    if (filter_var($url, FILTER_VALIDATE_URL) === false) {
      throw new UrlParserException('The url ' . $url . ' is invalid');
    }

    $this->url = $url;

    $this->createRequest();

    return $this;
  }

  /**
   * Create the request with the URL provided
   * It is used on loop thru the pages
   */
  protected function createRequest()
  {
    $url_components = parse_url($this->url);
    parse_str($url_components['query'], $query);
    $this->request = Request::create($this->url, 'GET', $query);
  }

  /**
   * Set the bearear token on request
   * @param string $token
   * @return UrlParser
   */
  public function withBearerToken(string $token): UrlParser
  {

    $this->bearerToken = $token;

    return $this;
  }

  /**
   * Set the content as a bootstrap 3 standard
   * @return UrlParser
   */
  public function withBootstrapThree(): UrlParser
  {
    $this->bootstrapThree = true;
    return $this;
  }

  /**
   * Set a delay between each page request
   * @param int $seconds
   * @return UrlParser
   */
  public function withDelay(int $seconds): UrlParser
  {
    $this->delay = $seconds;
    return $this;
  }

  /**
   * Makes the request itself and create the results
   * @return iterable
   * @throws UrlParserException
   */
  public function load(): iterable
  {

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
        sleep($this->delay);
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
      return ['Authorization' => 'Bearer ' . $this->bearerToken];
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

    if ($this->bootstrapThree) {
      return $this->parseBootstrapThreeResult($json);
    }

    $this->result->push($json->data);

    if (empty($json->next_page_url)) {
      return '';
    }

    return $json->next_page_url . '&' . urldecode(http_build_query($this->request->except('page')));
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

    return $json->meta->pagination->links->next . '&' . urldecode(http_build_query($this->request->except('page')));
  }
}
