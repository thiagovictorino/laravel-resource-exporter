<?php

namespace Victorino\ResourceExporter\Tests\Feature;


use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Illuminate\Support\Facades\Storage;
use Victorino\ResourceExporter\ResourceExporter;
use Victorino\ResourceExporter\Tests\TestCase;

class ResourseExporterTest extends TestCase
{

  protected function mockAPIResponse()
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
                    "username": "testeroot 1",
                    "email": "teste1@test.com",
                    "email_verified_at": "2020-01-29 17:31:26",
                    "created_at": "2020-01-29 17:31:18",
                    "updated_at": "2020-01-29 17:31:18",
                    "block": 0,
                    "terms_validation": 1,
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
                        "username": "testeroot 2",
                        "email": "teste2@test.com",
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

  }

  /**
   * @test
   */
  public function it_will_create_a_csv_file()
  {
    $this->mockAPIResponse();

    Storage::fake('local');

    /**
     * @var $resourceExporter ResourceExporter
     */

    $result = \ResourceExporter::endpoint('http://localhost:8082/test?sort=-id&filter[bookable_type]=properties')
      ->toCSV();

    Storage::disk('local')->assertExists($result);

  }
}