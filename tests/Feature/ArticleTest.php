<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\ArticleTranslation;
use App\Models\SiteLanguage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ArticleTest extends TestCase
{
    use RefreshDatabase;

    protected $apiKey;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->apiKey = config('app.api_key');
        
        // Create languages
        SiteLanguage::create(['language_code' => 'en', 'language_name' => 'English']);
        SiteLanguage::create(['language_code' => 'et', 'language_name' => 'Eesti']);
        
        // Create public article with translations
        $article = Article::create([
            'node_type' => 'article',
            'visibility' => 'public'
        ]);
        
        $article->translations()->create([
            'language_code' => 'en',
            'title' => 'Welcome Post',
            'path' => 'welcome',
            'content' => 'Welcome to our website',
            'status' => 'published'
        ]);
        
        $article->translations()->create([
            'language_code' => 'et',
            'title' => 'Tere tulemast',
            'path' => 'tere',
            'content' => 'Tere tulemast meie veebisaidile',
            'status' => 'published'
        ]);
        
        // Create private article
        $privateArticle = Article::create([
            'node_type' => 'user_agreement',
            'visibility' => 'private'
        ]);
        
        $privateArticle->translations()->create([
            'language_code' => 'en',
            'title' => 'Terms of Service',
            'path' => 'terms',
            'content' => 'Our terms of service...',
            'status' => 'published'
        ]);
    }

    /** @test */
    public function it_can_list_public_articles()
    {
        $response = $this->getJson('/articles');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'article_translation_id',
                        'title',
                        'path',
                        'language_code',
                        'article' => ['article_id', 'node_type', 'visibility']
                    ]
                ],
                'meta',
                'links'
            ])
            ->assertJsonCount(2, 'data');
    }

    /** @test */
    public function it_can_list_all_articles_with_private_access()
    {
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/private/articles');
            
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data'); // All translations including private
    }

    /** @test */
    public function private_routes_require_api_key()
    {
        $response = $this->getJson('/private/articles');
        $response->assertStatus(401);
        
        $response = $this->withHeaders(['X-API-KEY' => 'invalid-key'])
            ->getJson('/private/articles');
        $response->assertStatus(401);
    }

    /** @test */
    public function it_can_get_article_by_path()
    {
        $response = $this->getJson('/articles/welcome');
        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Welcome Post',
                'path' => 'welcome'
            ]);
    }

    /** @test */
    public function it_cannot_get_private_article_without_key()
    {
        $response = $this->getJson('/articles/terms');
        $response->assertStatus(404);
    
    // Should be accessible via private route with API key
    $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
        ->getJson('/private/articles/terms');
    
    $response->assertStatus(200)
        ->assertJson([
            'title' => 'Terms of Service',
            'path' => 'terms'
        ]);
    }

    /** @test */
    public function it_can_filter_articles_by_title()
    {
        $response = $this->getJson('/articles?title=Welcome');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Welcome Post');
    }

    /** @test */
    public function it_can_filter_articles_by_language()
    {
        $response = $this->getJson('/articles?language_code=et');
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Tere tulemast');
    }

    /** @test */
    public function it_can_filter_articles_by_status()
    {
        // Create a draft article
        $article = Article::create([
            'node_type' => 'article',
            'visibility' => 'public'
        ]);
        
        $article->translations()->create([
            'language_code' => 'en',
            'title' => 'Draft Article',
            'path' => 'draft',
            'content' => 'Draft content',
            'status' => 'draft'
        ]);
        
        // Use private endpoint to see draft articles
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->getJson('/private/articles?status=draft');
        
        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.title', 'Draft Article');
    }

    /** @test */
    public function it_can_filter_by_date_range()
    {
        $response = $this->getJson('/articles?from=2025-01-01&to=2025-12-31');
        $response->assertStatus(200)
            ->assertJsonCount(2, 'data'); // Both seeded articles
    }

    /** @test */
    public function it_sorts_articles_by_updated_at_descending()
    {
        // Update the welcome post to make it the most recent
        $welcome = ArticleTranslation::where('path', 'welcome')->first();
        $welcome->update(['title' => 'Updated Welcome']);
        
        $response = $this->getJson('/articles');
        $response->assertStatus(200)
            ->assertJsonPath('data.0.title', 'Updated Welcome');
    }

    /** @test */
    public function it_supports_pagination()
    {
        // Create enough articles for pagination
        $article = Article::create(['node_type' => 'article', 'visibility' => 'public']);
        for ($i = 0; $i < 15; $i++) {
            ArticleTranslation::create([
                'article_id' => $article->article_id,
                'language_code' => 'en',
                'title' => "Article $i",
                'path' => "article-$i",
                'content' => "Content $i",
                'status' => 'published'
            ]);
        }
        
        $response = $this->getJson('/articles?per_page=5');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.per_page', 5)
            ->assertJsonPath('meta.current_page', 1);
        
        $response = $this->getJson('/articles?per_page=5&page=2');
        $response->assertStatus(200)
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.current_page', 2);
    }

    /** @test */
    public function it_can_create_an_article()
    {
        $data = [
            'node_type' => 'article',
            'visibility' => 'public',
            'translations' => [
                [
                    'language_code' => 'en',
                    'title' => 'New Article',
                    'path' => 'new-article',
                    'content' => 'Article content',
                    'status' => 'published'
                ]
            ]
        ];
        
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/private/articles', $data);
            
        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'data' => [
                    'article_id',
                    'node_type',
                    'visibility',
                    'translations' => [
                        '*' => [
                            'article_translation_id',
                            'title',
                            'path',
                            'status'
                        ]
                    ]
                ]
            ])
            ->assertJsonPath('data.translations.0.title', 'New Article');
            
        $this->assertDatabaseHas('articles', [
            'node_type' => 'article',
            'visibility' => 'public'
        ]);
        
        $this->assertDatabaseHas('article_translations', [
            'path' => 'new-article',
            'status' => 'published'
        ]);
    }

    /** @test */
    public function it_validates_article_creation()
    {
        // Missing required fields
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/private/articles', []);
            
        $response->assertStatus(422)
            ->assertJsonValidationErrors([
                'node_type', 'visibility', 'translations'
            ]);
            
        // Invalid path (duplicate)
        $data = [
            'node_type' => 'article',
            'visibility' => 'public',
            'translations' => [
                [
                    'language_code' => 'en',
                    'title' => 'Duplicate Path',
                    'path' => 'welcome', // Already exists
                    'content' => 'Content',
                    'status' => 'published'
                ]
            ]
        ];
        
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->postJson('/private/articles', $data);
            
        $response->assertStatus(422)
            ->assertJsonValidationErrors(['translations.0.path']);
    }

    /** @test */
    public function it_can_update_an_article()
    {
        $article = Article::first();
        $translations = $article->translations;
        
        $data = [
            'visibility' => 'private',
            'translations' => [
                [
                    'article_translation_id' => $translations[0]->article_translation_id,
                    'title' => 'Updated Title',
                    'status' => 'unpublished'
                ],
                [
                    'article_translation_id' => $translations[1]->article_translation_id,
                    'title' => 'Uuendatud pealkiri',
                    'content' => 'Uuendatud sisu',
                    'status' => 'published'
                ]
            ]
        ];
        
        $response = $this->withHeaders(['X-API-KEY' => $this->apiKey])
            ->putJson("/private/articles/{$article->article_id}", $data);
            
        $response->assertStatus(200)
            ->assertJsonPath('data.visibility', 'private')
            ->assertJsonPath('data.translations.0.title', 'Updated Title')
            ->assertJsonPath('data.translations.1.title', 'Uuendatud pealkiri');
        
            
        $this->assertDatabaseHas('articles', [
            'article_id' => $article->article_id,
            'visibility' => 'private'
        ]);
        
        $this->assertDatabaseHas('article_translations', [
            'article_translation_id' => $article->translations->first()->article_translation_id,
            'title' => 'Updated Title',
            'status' => 'unpublished'
        ]);
    }
}