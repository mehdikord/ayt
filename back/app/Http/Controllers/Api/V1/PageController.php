<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\StaticPageResource;
use App\Models\StaticPage;
use App\Support\ApiResponse;

class PageController extends Controller
{
    public function about()
    {
        $page = StaticPage::query()
            ->where('page_key', 'about')
            ->where('is_active', true)
            ->firstOrFail();

        return ApiResponse::success(
            data: new StaticPageResource($page),
            message: 'About page fetched successfully.'
        );
    }
}
