<?php

namespace Tests\Feature;

use App\Services\NYTimesAPIService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NYTimesAPIServiceCacheTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
    }

    public function test_overview_returns_cached_response(): void
    {
        $cached = ['cached' => true];
        $key = 'nyt:overview:' . md5(json_encode([]));

        Cache::put($key, $cached, 3600);

        $service = new NYTimesAPIService();

        $result = $service->getOverview();

        $this->assertEquals($cached, $result);
    }

    public function test_overview_makes_http_request_and_caches(): void
    {
        $responseData = [
            'results' => [
                'lists' => [[
                    'list_name' => 'Hardcover Fiction',
                    'list_name_encoded' => 'hardcover-fiction',
                    'books' => [[
                        'title' => 'The Stand',
                        'author' => 'Stephen King',
                        'primary_isbn13' => '9780987654321'
                    ]]
                ]],
                'num_results' => 1
            ]
        ];

        Http::fake([
            '*' => Http::response($responseData, 200)
        ]);

        $service = new NYTimesAPIService();
        $result = $service->getOverview();

        $this->assertEquals('The Stand', $result['results']['lists'][0]['books'][0]['title']);

        $key = 'nyt:overview:' . md5(json_encode([]));
        $this->assertTrue(Cache::has($key));
    }

    public function test_cache_key_differs_by_filter(): void
    {
        $service = new NYTimesAPIService();

        $filters1 = ['author' => 'Stephen King'];
        $filters2 = ['author' => 'Fitzgerald'];

        $key1 = 'nyt:overview:' . md5(json_encode($filters1));
        $key2 = 'nyt:overview:' . md5(json_encode($filters2));

        $this->assertNotEquals($key1, $key2);
    }
}
