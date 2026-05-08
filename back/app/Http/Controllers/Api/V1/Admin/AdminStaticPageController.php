<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticPage;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class AdminStaticPageController extends Controller
{
    public function index()
    {
        $pages = StaticPage::query()->orderBy('page_key')->get();

        return ApiResponse::success(
            data: $pages->map(fn (StaticPage $p) => $this->serializePage($p)),
            message: 'Static pages fetched successfully.',
        );
    }

    public function show(int $id)
    {
        $page = StaticPage::query()->findOrFail($id);

        return ApiResponse::success(
            data: $this->serializePage($page),
            message: 'Static page fetched successfully.',
        );
    }

    public function showByKey(string $pageKey)
    {
        $page = StaticPage::query()->where('page_key', $pageKey)->firstOrFail();

        return ApiResponse::success(
            data: $this->serializePage($page),
            message: 'Static page fetched successfully.',
        );
    }

    public function update(Request $request, int $id)
    {
        $page = StaticPage::query()->findOrFail($id);

        $validated = $request->validate([
            'title' => ['sometimes', 'required', 'string', 'max:180'],
            'content' => ['sometimes', 'required', 'string'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        $page->fill([
            'title' => $validated['title'] ?? $page->title,
            'content' => $validated['content'] ?? $page->content,
            'is_active' => $validated['is_active'] ?? $page->is_active,
        ])->save();

        return ApiResponse::success(
            data: $this->serializePage($page->fresh()),
            message: 'Static page updated successfully.',
        );
    }

    private function serializePage(StaticPage $p): array
    {
        return [
            'id' => $p->id,
            'page_key' => $p->page_key,
            'title' => $p->title,
            'content' => $p->content,
            'is_active' => $p->is_active,
            'created_at' => $p->created_at?->toIso8601String(),
            'updated_at' => $p->updated_at?->toIso8601String(),
        ];
    }
}
