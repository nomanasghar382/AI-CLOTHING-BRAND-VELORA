<?php

namespace App\Http\Controllers\Api\V1\Catalog;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Services\Catalog\SearchService;
use App\Traits\RespondsWithApi;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SearchController extends Controller
{
  use RespondsWithApi;

  public function __construct(private readonly SearchService $search)
  {
  }

  public function index(Request $request): JsonResponse
  {
    $page = $this->search->search(
      $request->all(),
      $request->user()?->id,
      $request->header('X-Session-Id')
    );

    return $this->success([
      'items' => ProductResource::collection($page->items()),
      'meta' => [
        'current_page' => $page->currentPage(),
        'last_page' => $page->lastPage(),
        'per_page' => $page->perPage(),
        'total' => $page->total(),
      ],
    ]);
  }

  public function suggestions(Request $request): JsonResponse
  {
    return $this->success([
      'suggestions' => $this->search->suggestions((string) $request->string('q')),
      'popular' => $this->search->popular(),
      'recent' => $this->search->recent($request->user()?->id, $request->header('X-Session-Id')),
    ]);
  }
}
