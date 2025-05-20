<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class NYTBestSellersOverviewTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();
    }

    /**
     * Test successful retrieval of best sellers overview without filters
     */
    public function test_successful_overview_retrieval(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Great Gatsby',
                                    'author' => 'F. Scott Fitzgerald',
                                    'primary_isbn13' => '9781234567890'
                                ],
                                [
                                    'title' => 'The Stand',
                                    'author' => 'Stephen King',
                                    'primary_isbn13' => '9780987654321'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test filtering by published date
     */
    public function test_filter_by_published_date(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?published_date=2024-03-01');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Great Gatsby',
                                    'author' => 'F. Scott Fitzgerald',
                                    'primary_isbn13' => '9781234567890'
                                ],
                                [
                                    'title' => 'The Stand',
                                    'author' => 'Stephen King',
                                    'primary_isbn13' => '9780987654321'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test filtering by author
     */
    public function test_filter_by_author(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?author=Stephen%20King');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Stand',
                                    'author' => 'Stephen King',
                                    'primary_isbn13' => '9780987654321'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test filtering by ISBN
     */
    public function test_filter_by_isbn(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?isbn[]=9781234567890&isbn[]=9780987654321');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Great Gatsby',
                                    'author' => 'F. Scott Fitzgerald',
                                    'primary_isbn13' => '9781234567890'
                                ],
                                [
                                    'title' => 'The Stand',
                                    'author' => 'Stephen King',
                                    'primary_isbn13' => '9780987654321'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test filtering by title
     */
    public function test_filter_by_title(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?title=The%20Great%20Gatsby');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Great Gatsby',
                                    'author' => 'F. Scott Fitzgerald',
                                    'primary_isbn13' => '9781234567890'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test multiple filters combined
     */
    public function test_multiple_filters(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?published_date=2024-03-01&author=Stephen%20King&title=The%20Stand');

        $response->assertStatus(200)
            ->assertJson([
                'results' => [
                    'lists' => [
                        [
                            'list_name' => 'Hardcover Fiction',
                            'list_name_encoded' => 'hardcover-fiction',
                            'books' => [
                                [
                                    'title' => 'The Stand',
                                    'author' => 'Stephen King',
                                    'primary_isbn13' => '9780987654321'
                                ]
                            ]
                        ]
                    ],
                    'num_results' => 1
                ]
            ]);
    }

    /**
     * Test invalid published date format
     */
    public function test_invalid_published_date_format(): void
    {
        $this->mockNYTimesAPI();

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview?published_date=invalid-date');

        $response->assertStatus(422);
    }

    /**
     * Test empty response handling
     */
    public function test_empty_response_handling(): void
    {
        Http::fake([
            '*' => Http::response([
                'results' => []
            ], 200)
        ]);

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview');

        $response->assertStatus(200)
            ->assertJson([
                'results' => []
            ]);
    }

    /**
     * Test API error handling
     */
    public function test_api_error_handling(): void
    {
        Http::fake([
            '*' => Http::response([
                'error' => 'Failed to fetch data from NYT API'
            ], 500)
        ]);

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'Failed to fetch data from NYT API'
            ]);
    }

    /**
     * Test exception handling
     */
    public function test_exception_handling(): void
    {
        Http::fake([
            '*' => function() {
                throw new \Exception('API Error');
            }
        ]);

        $response = $this->getJson('/api/v1/nyt/bestsellers/overview');

        $response->assertStatus(500)
            ->assertJson([
                'error' => 'API Error'
            ]);
    }

    /**
     * Mock the NYT API responses
     */
    private function mockNYTimesAPI(): void
    {
        $mockResponse = [
            'results' => [
                'lists' => [
                    [
                        'list_name' => 'Hardcover Fiction',
                        'list_name_encoded' => 'hardcover-fiction',
                        'books' => [
                            [
                                'title' => 'The Great Gatsby',
                                'author' => 'F. Scott Fitzgerald',
                                'primary_isbn13' => '9781234567890'
                            ],
                            [
                                'title' => 'The Stand',
                                'author' => 'Stephen King',
                                'primary_isbn13' => '9780987654321'
                            ]
                        ]
                    ]
                ],
                'num_results' => 1
            ]
        ];

        Http::fake([
            '*' => Http::response($mockResponse, 200)
        ]);
    }
}
