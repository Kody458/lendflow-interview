<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\NYTimesAPIService;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\NYTBestSellersRequest;
use Illuminate\Http\Request;

class NYTBestSellersController extends Controller
{
    public function __construct(protected NYTimesAPIService $nytimesAPIService)
    {
        //
    }

    /**
     * Get the overview of all best sellers lists. Available filters:
     * - published_date
     * - author
     * - isbn
     * - title
     *
     * @param NYTBestSellersRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function overview(NYTBestSellersRequest $request): JsonResponse
    {
        try {
            $data = $this->nytimesAPIService->getOverview($request->validated());

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], $e->getCode() ?: 500);
        }
    }

    /**
     * NOTE: If this was a production app another function could be made
     * here for the list endpoint.
     */
}
