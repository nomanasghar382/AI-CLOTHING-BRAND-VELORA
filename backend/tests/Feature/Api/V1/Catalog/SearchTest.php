<?php

namespace Tests\Feature\Api\V1\Catalog;

use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchTest extends TestCase
{
  use RefreshDatabase;

  public function test_search_suggestions_endpoint_returns_envelope(): void
  {
    $this->seed(RoleSeeder::class);

    $this->getJson('/api/v1/search/suggestions?q=abaya')
      ->assertOk()
      ->assertJsonPath('success', true)
      ->assertJsonStructure(['data' => ['suggestions', 'popular', 'recent']]);
  }
}
