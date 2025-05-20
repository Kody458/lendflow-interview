<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\Response;
use Carbon\Carbon;
use Carbon\CarbonInterface;

class NYTimesAPIService
{
    public function getOverview(array $filters = []): array
    {
        $cacheKey = $this->generateCacheKey($filters);
        $ttl = now()->diffInSeconds($this->nextRefreshTime());

        return Cache::remember($cacheKey, $ttl, function () use ($filters) {
            $queryParams = $this->buildQueryParameters($filters);
            return $this->makeRequest($queryParams, $filters);
        });
    }

    private function buildQueryParameters(array $filters): array
    {
        $params = [
            'api-key' => config('api.nytimes_api_key')
        ];

        if (isset($filters['published_date'])) {
            $params['published_date'] = $filters['published_date'];
        }

        return $params;
    }

    private function makeRequest(array $queryParams, array $filters): array
    {
        $endpoint = config('api.nytimes_api_endpoint') . config('api.nytimes_api_overview_endpoint');

        try {
            $response = Http::get($endpoint, $queryParams);

            if ($response->failed()) {
                Log::error('NYT API request failed', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                throw new \Exception('Failed to fetch data from NYT API', $response->status());
            }

            return $this->processSuccessfulResponse($response, $filters);
        } catch (\Exception $e) {
            Log::error('NYT API request exception', [
                'message' => $e->getMessage()
            ]);

            throw $e;
        }
    }

    private function processSuccessfulResponse(Response $response, array $filters): array
    {
        $responseData = $response->json();

        if (!isset($responseData['results']['lists'])) {
            return $responseData;
        }

        $filteredLists = [];

        foreach ($responseData['results']['lists'] as $list) {
            if (!isset($list['books'])) {
                continue;
            }

            $filteredBooks = array_filter($list['books'], function ($book) use ($filters) {
                if (isset($filters['author']) &&
                    !empty($filters['author']) &&
                    stripos($book['author'] ?? '', $filters['author']) === false) {
                    return false;
                }

                if (isset($filters['isbn']) &&
                    !empty($filters['isbn']) &&
                    is_array($filters['isbn'])) {
                    $bookIsbns = array_map('trim', explode(';', $book['primary_isbn13'] ?? ''));
                    $hasMatchingIsbn = false;
                    foreach ($filters['isbn'] as $isbn) {
                        if (in_array($isbn, $bookIsbns)) {
                            $hasMatchingIsbn = true;
                            break;
                        }
                    }
                    if (!$hasMatchingIsbn) {
                        return false;
                    }
                }

                if (isset($filters['title']) &&
                    !empty($filters['title']) &&
                    stripos($book['title'] ?? '', $filters['title']) === false) {
                    return false;
                }

                return true;
            });

            if (!empty($filteredBooks)) {
                $list['books'] = array_values($filteredBooks);
                $filteredLists[] = $list;
            }
        }

        $responseData = array_merge($responseData, [
            'results' => array_merge($responseData['results'], [
                'lists' => $filteredLists,
                'num_results' => count($filteredLists)
            ])
        ]);

        return $responseData;
    }

    private function generateCacheKey(array $filters): string
    {
        return 'nyt:overview:' . md5(json_encode($filters));
    }

    private function nextRefreshTime(): Carbon
    {
        $now = now()->setTimezone(config('api.nytimes_timezone'));
        $refreshDay = constant(CarbonInterface::class . '::' . config('api.nytimes_refresh_day'));
        [$hours, $minutes] = explode(':', config('api.nytimes_refresh_time'));
        
        $next = $now->copy()->next($refreshDay)->setTime((int)$hours, (int)$minutes);

        if ($now->greaterThanOrEqualTo($next)) {
            $next->addWeek();
        }

        return $next;
    }
}
