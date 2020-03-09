<?php


namespace thiagovictorino\ResourceExporter\Url;


use Illuminate\Http\Request;
use thiagovictorino\ResourceExporter\Exceptions\UrlParserException;

class Builder
{


  /**
   * @var string
   */
  protected $payload;

  /**
   * @var int
   */
  protected $delay = 0;

  /**
   * The endpoint of where the data will be get
   * @var $endpoint string
   */
  protected $endpoint;

  /**
   * @var $bearerToken string?
   */
  protected $bearerToken;

  /**
   * @var Request
   */
  protected $request;

  public function __construct()
  {
    $this->setPayload(config("resource-exporter.payload"));
  }

  /**
   * @param string $endpoint
   * @return Parser
   * @throws UrlParserException
   */
  public function setEndpoint(string $endpoint): Builder
  {

    if (filter_var($endpoint, FILTER_VALIDATE_URL) === false) {
      throw new UrlParserException('The url ' . $endpoint . ' is invalid');
    }

    $this->endpoint = $endpoint;
    $this->createRequest();

    return $this;
  }

  /**
   * Set the bearear token on request
   * @param string $token
   * @return Builder
   */
  public function setBearerToken(string $token): Builder
  {
    $this->bearerToken = $token;
    return $this;
  }

  /**
   * Set a delay between each page request
   * @param int $seconds
   * @return Builder
   */
  public function setDelay(int $seconds): Builder
  {
    $this->delay = $seconds;
    return $this;
  }

  /**
   * Set the resource payload type
   * @param $payload string
   * @return Builder
   */
  public function setPayload(string $payload): Builder {
    $this->payload = $payload;
    return $this;
  }

  /**
   * @return string
   */
  public function getPayload(): string
  {
    return $this->payload;
  }

  /**
   * @return int
   */
  public function getDelay(): int
  {
    return $this->delay;
  }

  /**
   * @return string
   */
  public function getEndpoint(): string
  {
    return $this->endpoint;
  }

  /**
   * @return string
   */
  public function getBearerToken(): string
  {
    return $this->bearerToken;
  }

  /**
   * @return Request
   */
  public function getRequest(): Request
  {
    return $this->request;
  }

  /**
   * Create the request with the URL provided
   * It is used on loop thru the pages
   */
  protected function createRequest()
  {
    $url_components = parse_url($this->endpoint);
    parse_str($url_components['query'], $query);
    $this->request = Request::create($this->endpoint, 'GET', $query);
  }

}