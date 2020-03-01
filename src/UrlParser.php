<?php

namespace thiagovictorino\ResourceExporter;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use thiagovictorino\ResourceExporter\Exceptions\UrlParserException;
use function GuzzleHttp\Psr7\str;

class UrlParser
{
    protected $url;

    protected $bearerToken;

    protected $httpClient;

    protected $delay = 0;

    protected $bootstrapThree = false;

    protected $request;

    protected $result;

    public function __construct()
    {
        $this->httpClient = resolve(Client::class);
        $this->result = collect();
    }

    public function endpoint(string $url): UrlParser
    {

        if (filter_var($url, FILTER_VALIDATE_URL) === false) {
            throw new UrlParserException('The url '.$url.' is invalid');
        }

        $this->url = $url;

        $this->createRequest();

        return $this;
    }

    protected function createRequest()
    {
        $url_components = parse_url($this->url);
        parse_str($url_components['query'], $query);
        $this->request = Request::create($this->url, 'GET', $query);
    }

    public function withBearerToken(string $token): UrlParser
    {

        $this->bearerToken = $token;

        return $this;
    }

    public function withBootstrapThree(): UrlParser
    {
        $this->bootstrapThree = true;
        return $this;
    }

    public function withDelay(int $seconds): UrlParser
    {
        $this->delay = $seconds;
        return $this;
    }

    public function load(): iterable
    {

        $this->validateBeforeLoad();

        $this->sendRequest();

        return $this->result;
    }

    protected function sendRequest()
    {
        try {

            $response = $this->httpClient->request('GET', $this->url, [
              'headers' => $this->setHeaders()
            ]);

            $next_url = $this->setResult($response->getBody()->getContents());

            if(!empty($next_url)){
                $this->url = $next_url;
                sleep($this->delay);
                $this->sendRequest();
            }

        } catch (RequestException $e) {

            if ($e->hasResponse()) {
                throw new UrlParserException($e->getMessage().' - Response:'.str($e->getResponse()),$e->getCode(), $e);
            }

            throw new UrlParserException($e->getMessage().' - Request:'.str($e->getRequest()),$e->getCode(), $e);

        } catch (\Exception $e) {

            throw new UrlParserException($e->getMessage(),$e->getCode(), $e);

        }

    }

    protected function setHeaders()
    {
        return array_merge(
          [
            'Accept' => 'application/json'
          ],
          $this->setAuthHeaders()
        );
    }

    protected function setAuthHeaders(): array
    {

        if(!empty($this->bearerToken)){
            return ['Authorization' => 'Bearer ' . $this->bearerToken];
        }

        return [];
    }

    private function validateBeforeLoad()
    {
        if (empty($this->url)) {
            throw new UrlParserException('You must inform an URL at least before call load()');
        }
    }

    private function setResult(string $contents) : ?string
    {
        $json = json_decode($contents);

        if($this->bootstrapThree){
            return $this->parseBootstrapThreeResult($json);
        }

        $this->result->push($json->data);

        if(empty($json->next_page_url)) {
            return '';
        }

        return $json->next_page_url.'&'.urldecode(http_build_query($this->request->except('page')));
    }

    private function parseBootstrapThreeResult($json)
    {

        if (!isset($json->meta)) {
           throw new UrlParserException('This is not a Bootstrap 3 format');
        }
        $this->result->push($json->data);

        if(empty($json->meta->pagination->links->next)) {
            return '';
        }

        return $json->meta->pagination->links->next.'&'.urldecode(http_build_query($this->request->except('page')));
    }
}
