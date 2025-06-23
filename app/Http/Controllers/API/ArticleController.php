<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\ArticleListRequest;
use App\Http\Requests\ArticleStoreRequest;
use App\Http\Requests\ArticleShowRequest;
use App\Http\Requests\ArticleUpdateRequest;
use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService)
    {
    }

    /**
     * Get filtered list of articles
     */
    public function index(ArticleListRequest $request): JsonResponse
    {
        $isPrivate = $request->is('private/*');
        $filters = $request->validated();
        $perPage = $request->input('per_page', 10);
        
        $result = $this->articleService->getArticles(
            $isPrivate,
            $filters,
            $perPage
        );

        return response()->json($result);
    }

    /**
     * Get article by path
     */
    public function show(ArticleShowRequest $request): JsonResponse
    {
        // Use validated data
        $path = $request->validated()['path'];
        $isPrivate = $request->is('private/*');
        
        $article = $this->articleService->getArticleByPath($path, $isPrivate);
        
        if (!$article) {
            return response()->json(['message' => 'Article not found'], 404);
        }
        
        return response()->json($article);
    }

    /**
     * Create a new article
     */
    public function store(ArticleStoreRequest $request): JsonResponse
    {
        $article = $this->articleService->createArticle($request->validated());

        return response()->json([
            'message' => 'Article created successfully',
            'data' => $article
        ], 201);
    }

    /**
     * Update an existing article
     */
    public function update(ArticleUpdateRequest $request, Article $article): JsonResponse
    {
        $article = $this->articleService->updateArticle(
            $article, 
            $request->validated()
        );

        return response()->json([
            'message' => 'Article updated successfully',
            'data' => $article
        ]);
    }
}