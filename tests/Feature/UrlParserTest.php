<?php


namespace thiagovictorino\ResourceExporter\Tests\Feature;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Foundation\Auth\User;
use thiagovictorino\ResourceExporter\Exceptions\UrlParserException;
use thiagovictorino\ResourceExporter\Tests\TestCase;
use thiagovictorino\ResourceExporter\Url\Builder;
use thiagovictorino\ResourceExporter\Url\Parser;
use thiagovictorino\ResourceExporter\Url\PayloadType;
use function GuzzleHttp\Psr7\stream_for;

class UrlParserTest extends TestCase
{
  /**
   * @test
   */
  public function it_will_throws_an_url_exception()
  {

    $this->expectException(UrlParserException::class);

    $urlParser = resolve(Parser::class);
    $builder = resolve(Builder::class);
    $builder->setEndpoint('xxxxxxx');

    $urlParser->load($builder);
  }

  /**
   * @test
   */
  public function it_will_parse_an_resource_paginated()
  {

    $guzzle = \Mockery::mock(Client::class);
    $guzzle->shouldReceive('request')->withArgs([
      'GET',
      "http://localhost:8082/test?sort=-id&filter[bookable_type]=properties",
      [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]
    ])->once()->andReturn(
      new Response(
        200,
        [],
        '{
                  "current_page": 1,
                  "data": [{
                    "id": 1,
                    "username": "testeroot",
                    "email": "teste@test.com",
                    "email_verified_at": "2020-01-29 17:31:26",
                    "created_at": "2020-01-29 17:31:18",
                    "updated_at": "2020-01-29 17:31:18",
                    "block": 0,
                    "terms_validation": 0,
                    "validation_ip": null,
                    "validated_at": null,
                    "last_login": null,
                    "reengaged_at": null,
                    "person_id": 1
                  }],
                  "first_page_url": "http:\/\/localhost:8082\/test?page=1",
                  "from": 1,
                  "last_page": 2,
                  "last_page_url": "http:\/\/localhost:8082\/test?page=2",
                  "next_page_url": "http:\/\/localhost:8082\/test?page=2",
                  "path": "http:\/\/localhost:8082\/test",
                  "per_page": 1,
                  "prev_page_url": null,
                  "to": 1,
                  "total": 2
                }'
      )
    );

    $guzzle->shouldReceive('request')->withArgs([
      'GET',
      'http://localhost:8082/test?page=2&sort=-id&filter[bookable_type]=properties',
      [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]
    ])->once()->andReturn(
      new Response(
        200,
        [],
        '{
                      "current_page": 2,
                      "data": [{
                        "id": 1,
                        "username": "testeroot",
                        "email": "teste@test.com",
                        "email_verified_at": "2020-01-29 17:31:26",
                        "created_at": "2020-01-29 17:31:18",
                        "updated_at": "2020-01-29 17:31:18",
                        "block": 0,
                        "terms_validation": 0,
                        "validation_ip": null,
                        "validated_at": null,
                        "last_login": null,
                        "reengaged_at": null,
                        "person_id": 1
                      }],
                      "first_page_url": "http:\/\/localhost:8082\/test?page=1",
                      "from": 2,
                      "last_page": 2,
                      "last_page_url": "http:\/\/localhost:8082\/test?page=2",
                      "next_page_url": "",
                      "path": "http:\/\/localhost:8082\/test",
                      "per_page": 1,
                      "prev_page_url": null,
                      "to": 1,
                      "total": 2
                    }'));

    $this->instance(Client::class, $guzzle);

    $urlParser = resolve(Parser::class);
    $builder = resolve(Builder::class);
    $builder->setEndpoint('http://localhost:8082/test?sort=-id&filter[bookable_type]=properties');

    $result = $urlParser->load($builder);

    $this->assertCount(2, $result);

    $first_page = $result->first();

    $this->assertObjectHasAttribute('id', $first_page[0]);
    $this->assertObjectHasAttribute('username', $first_page[0]);
    $this->assertObjectHasAttribute('email', $first_page[0]);
    $this->assertObjectHasAttribute('person_id', $first_page[0]);
  }

  /**
   * @test
   */
  public function it_will_throws_an_exception_when_parse_bootstrap_three()
  {

    $this->expectException(UrlParserException::class);
    $guzzle = \Mockery::mock(Client::class);
    $guzzle->shouldReceive('request')->withArgs([
      'GET',
      "http://localhost:8082/test?sort=-id&filter[bookable_type]=properties",
      [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]
    ])->once()->andReturn(new Response(200, [], '{"current_page": 1,"data":[{"id":1,"username":"testeroot","email":"teste@test.com","email_verified_at":"2020-01-29 17:31:26","created_at":"2020-01-29 17:31:18","updated_at":"2020-01-29 17:31:18","block":0,"terms_validation":0,"validation_ip":null,"validated_at":null,"last_login":null,"reengaged_at":null,"person_id":1}],"first_page_url":"http:\/\/localhost:8082\/test?page=1","from":1,"last_page":2,"last_page_url":"http:\/\/localhost:8082\/test?page=2","next_page_url":"http:\/\/localhost:8082\/test?page=2","path":"http:\/\/localhost:8082\/test","per_page":1,"prev_page_url":null,"to":1,"total":2}'));


    $this->instance(Client::class, $guzzle);

    $urlParser = resolve(Parser::class);
    $builder = resolve(Builder::class);
    $builder->setEndpoint('http://localhost:8082/test?sort=-id&filter[bookable_type]=properties')
            ->withPayload(PayloadType::BOOTSTRAP3);

    $urlParser->load($builder);

  }

  /**
   * @test
   */
  public function it_will_parse_an_resource_paginated_with_bootstrap_three()
  {

    $guzzle = \Mockery::mock(Client::class);
    $guzzle->shouldReceive('request')->withArgs([
      'GET',
      "http://localhost:8082/test?sort=-id&filter[bookable_type]=properties",
      [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]
    ])->once()->andReturn(new Response(200, [], '{
                  "current_page": 1,
                  "data": [{
                    "id": 1,
                    "username": "testeroot",
                    "email": "teste@test.com",
                    "email_verified_at": "2020-01-29 17:31:26",
                    "created_at": "2020-01-29 17:31:18",
                    "updated_at": "2020-01-29 17:31:18",
                    "block": 0,
                    "terms_validation": 0,
                    "validation_ip": null,
                    "validated_at": null,
                    "last_login": null,
                    "reengaged_at": null,
                    "person_id": 1
                  }],
                  "meta": {
                    "pagination": {
                      "first_page_url": "http:\/\/localhost:8082\/test?page=1",
                      "from": 2,
                      "last_page": 2,
                      "last_page_url": "http:\/\/localhost:8082\/test?page=2",
                      "links": {
                        "next" : "http:\/\/localhost:8082\/test?page=2"
                      },
                      "next_page_url": "http:\/\/localhost:8082\/test?page=2",
                      "path": "http:\/\/localhost:8082\/test",
                      "per_page": 1,
                      "prev_page_url": null,
                      "to": 1,
                      "total": 2
                    }
                  }
                }'));

    $guzzle->shouldReceive('request')->withArgs([
      'GET',
      'http://localhost:8082/test?page=2&sort=-id&filter[bookable_type]=properties',
      [
        'headers' => [
          'Accept' => 'application/json'
        ]
      ]
    ])->once()->andReturn(new Response(200,
        [],
        '{
                  "current_page": 2,
                  "data": [{
                    "id": 2,
                    "username": "testeroot",
                    "email": "teste@test.com",
                    "email_verified_at": "2020-01-29 17:31:26",
                    "created_at": "2020-01-29 17:31:18",
                    "updated_at": "2020-01-29 17:31:18",
                    "block": 0,
                    "terms_validation": 0,
                    "validation_ip": null,
                    "validated_at": null,
                    "last_login": null,
                    "reengaged_at": null,
                    "person_id": 1
                  }],
                  "meta": {
                    "pagination": {
                      "first_page_url": "http:\/\/localhost:8082\/test?page=1",
                      "from": 2,
                      "last_page": 2,
                      "last_page_url": "http:\/\/localhost:8082\/test?page=2",
                      "next_page_url": "",
                      "links": {
                        "next" : ""
                      },
                      "path": "http:\/\/localhost:8082\/test",
                      "per_page": 1,
                      "prev_page_url": null,
                      "to": 1,
                      "total": 2
                    }
                  }
                }')
    );

    $this->instance(Client::class, $guzzle);

    $urlParser = resolve(Parser::class);
    $builder = resolve(Builder::class);
    $builder->setEndpoint('http://localhost:8082/test?sort=-id&filter[bookable_type]=properties')
      ->withPayload(PayloadType::BOOTSTRAP3);

    $result = $urlParser->load($builder);

    $this->assertCount(2, $result);

    $first_page = $result->first();

    $this->assertObjectHasAttribute('id', $first_page[0]);
    $this->assertObjectHasAttribute('username', $first_page[0]);
    $this->assertObjectHasAttribute('email', $first_page[0]);
    $this->assertObjectHasAttribute('person_id', $first_page[0]);
  }
}