<?php


namespace thiagovictorino\ResourceExporter\Tests\Feature;

use Illuminate\Foundation\Auth\User;
use thiagovictorino\ResourceExporter\Tests\TestCase;

class UrlParserTest extends TestCase
{
    /**
     * @test
     */
    public function it_will_parse_an_resource_paginated() {
        $users = factory(User::class, 30)->make();
        dd(User::where(1,1)->paginate(10));
    }
}