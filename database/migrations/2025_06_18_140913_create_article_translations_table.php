<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('article_translations', function (Blueprint $table) {
            $table->id('article_translation_id');
            
            // Fix foreign key reference
            $table->unsignedBigInteger('article_id');
            
            $table->char('language_code', 2);
            $table->string('title', 70);
            $table->string('path', 70);
            $table->string('summary', 180)->nullable();
            $table->string('keywords', 255)->nullable();
            $table->longText('content');
            $table->enum('status', ['draft', 'published', 'unpublished']);
            $table->dateTime('unpublished_at')->nullable();
            $table->integer('created_by')->nullable();
            $table->integer('modified_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Correct foreign keys
            $table->foreign('article_id')
                ->references('article_id')
                ->on('articles')
                ->onDelete('cascade');
                
            $table->foreign('language_code')
                ->references('language_code')
                ->on('site_languages');
            
            // Unique constraints
            $table->unique(['language_code', 'path']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('article_translations');
    }
};
