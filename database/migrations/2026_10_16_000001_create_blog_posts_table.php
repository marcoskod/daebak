<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('blog_posts', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->json('translations');                       // {"en":{"title","excerpt","content"},"fr":…,"ko":…}
            $table->string('category', 30)->default('culture');
            $table->string('cover')->nullable();
            $table->string('author', 80)->nullable();
            $table->json('tags')->nullable();
            $table->json('products')->nullable();               // slugs de produits liés
            $table->unsignedSmallInteger('reading_minutes')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blog_posts');
    }
};
