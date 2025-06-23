<?php

namespace Database\Seeders;

use App\Models\SiteLanguage;
use App\Models\Article;
use App\Models\ArticleTranslation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create languages
        $languages = [
            ['language_code' => 'et', 'language_name' => 'Eesti keel'],
            ['language_code' => 'en', 'language_name' => 'English'],
            ['language_code' => 'ru', 'language_name' => 'Русский'],
            ['language_code' => 'de', 'language_name' => 'Deutsch'],
        ];
        
        foreach ($languages as $lang) {
            SiteLanguage::create($lang);
        }

        // Create articles
        $articles = [
            [
                'node_type' => 'article',
                'visibility' => 'public',
                'translations' => [
                    [
                        'language_code' => 'et',
                        'title' => 'Laravelli kasutuselevõtt',
                        'path' => 'laravelli-kasutuselevott',
                        'summary' => 'Lühike ülevaade Laravelli raamistiku kasutamisest',
                        'keywords' => 'laravel, php, raamistik',
                        'content' => 'Laravel on tänapäeval üks populaarsemaid PHP raamistike. Selle kasutamine võimaldab kiiresti arendada robustseid veebirakendusi.',
                        'status' => 'published'
                    ],
                    [
                        'language_code' => 'en',
                        'title' => 'Introduction to Laravel',
                        'path' => 'introduction-to-laravel',
                        'summary' => 'A brief overview of using the Laravel framework',
                        'keywords' => 'laravel, php, framework',
                        'content' => 'Laravel is one of the most popular PHP frameworks today. Using it allows for rapid development of robust web applications.',
                        'status' => 'published'
                    ],
                    [
                        'language_code' => 'de',
                        'title' => 'Einführung in Laravel',
                        'path' => 'einfuhrung-in-laravel',
                        'summary' => 'Ein kurzer Überblick über die Verwendung des Laravel-Frameworks',
                        'keywords' => 'laravel, php, framework',
                        'content' => 'Laravel ist heute eines der beliebtesten PHP-Frameworks. Die Verwendung ermöglicht die schnelle Entwicklung robuster Webanwendungen.',
                        'status' => 'draft'
                    ]
                ]
            ],
            [
                'node_type' => 'user_agreement',
                'visibility' => 'private',
                'translations' => [
                    [
                        'language_code' => 'et',
                        'title' => 'Kasutajatingimused',
                        'path' => 'kasutajatingimused',
                        'summary' => 'Meie teenuse kasutamise tingimused',
                        'keywords' => 'tingimused, leping, kasutamine',
                        'content' => 'Enne meie teenuse kasutamist palume tutvuda kasutajatingimustega. Need reguleerivad teie õigusi ja kohustusi.',
                        'status' => 'published'
                    ],
                    [
                        'language_code' => 'en',
                        'title' => 'Terms of Service',
                        'path' => 'terms-of-service',
                        'summary' => 'Terms for using our service',
                        'keywords' => 'terms, agreement, usage',
                        'content' => 'Before using our service, please read our Terms of Service. These govern your rights and responsibilities.',
                        'status' => 'published'
                    ]
                ]
            ],
            [
                'node_type' => 'article',
                'visibility' => 'public',
                'translations' => [
                    [
                        'language_code' => 'et',
                        'title' => 'REST API arendus Laravellis',
                        'path' => 'rest-api-arendus',
                        'summary' => 'Kuidas luua RESTful API Laravelli raamistikus',
                        'keywords' => 'rest, api, laravel, backend',
                        'content' => 'Laravel pakub võimsat REST API arenduse raamistikku, mis hõlmab marsruutimist, kontrollereid ja ressursside vormindamist.',
                        'status' => 'published',
                        'unpublished_at' => now()->addMonths(6)
                    ],
                    [
                        'language_code' => 'en',
                        'title' => 'REST API Development in Laravel',
                        'path' => 'rest-api-development',
                        'summary' => 'How to create RESTful APIs with Laravel framework',
                        'keywords' => 'rest, api, laravel, backend',
                        'content' => 'Laravel provides a powerful framework for REST API development that includes routing, controllers, and resource formatting.',
                        'status' => 'published'
                    ],
                    [
                        'language_code' => 'ru',
                        'title' => 'Разработка REST API в Laravel',
                        'path' => 'rest-api-razrabotka',
                        'summary' => 'Как создать RESTful API с помощью фреймворка Laravel',
                        'keywords' => 'rest, api, laravel, бэкенд',
                        'content' => 'Laravel предоставляет мощную среду для разработки REST API, которая включает маршрутизацию, контроллеры и форматирование ресурсов.',
                        'status' => 'unpublished',
                        'unpublished_at' => now()
                    ]
                ]
            ]
        ];

        foreach ($articles as $articleData) {
            $article = Article::create([
                'node_type' => $articleData['node_type'],
                'visibility' => $articleData['visibility']
            ]);

            foreach ($articleData['translations'] as $translation) {
                ArticleTranslation::create(array_merge($translation, [
                    'article_id' => $article->article_id,
                    'created_by' => 1,
                    'modified_by' => 1
                ]));
            }
        }

        // Additional articles with different statuses for testing
        $draftArticle = Article::create([
            'node_type' => 'article',
            'visibility' => 'public'
        ]);

        ArticleTranslation::create([
            'article_id' => $draftArticle->article_id,
            'language_code' => 'en',
            'title' => 'Draft Article',
            'path' => 'draft-article',
            'content' => 'This is a draft article content',
            'status' => 'draft',
            'created_by' => 1,
            'modified_by' => 1
        ]);

        $unpublishedArticle = Article::create([
            'node_type' => 'page',
            'visibility' => 'private'
        ]);

        ArticleTranslation::create([
            'article_id' => $unpublishedArticle->article_id,
            'language_code' => 'et',
            'title' => 'Avaldamata artikkel',
            'path' => 'avaldamata-artikkel',
            'content' => 'See artikkel on avaldamata olekus',
            'status' => 'unpublished',
            'unpublished_at' => now()->subDays(10),
            'created_by' => 1,
            'modified_by' => 1
        ]);

        $this->command->info('Database seeded successfully!');
        $this->command->info('Total articles: '.Article::count());
        $this->command->info('Total translations: '.ArticleTranslation::count());
    }
}