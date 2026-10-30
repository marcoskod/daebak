<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('description');
            $table->string('icon')->nullable()->after('translations');
            $table->string('image')->nullable()->after('icon');
            $table->unsignedSmallInteger('sort_order')->default(0)->after('image');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->json('translations')->nullable()->after('description');      // {"en":{"name","short","description"},"fr":…,"ko":…}
            $table->string('product_type', 40)->nullable()->after('translations'); // tee, hoodie, mug…
            $table->string('provider', 30)->nullable()->after('product_type');     // printful, printify, gelato…
            $table->string('provider_sku', 80)->nullable()->after('provider');
            $table->json('variants')->nullable()->after('provider_sku');           // {"sizes":[…],"colors":[{key,hex,image}]}
            $table->json('tags')->nullable()->after('variants');                   // motifs / thèmes (hangul, hanbok, k-pop…)
            $table->string('motif', 40)->nullable()->after('tags');
            $table->string('badge', 30)->nullable()->after('motif');               // bestseller, new, limited
            $table->decimal('rating', 2, 1)->default(0)->after('badge');
            $table->unsignedInteger('reviews_count')->default(0)->after('rating');
            $table->unsignedTinyInteger('production_min')->default(2)->after('reviews_count');
            $table->unsignedTinyInteger('production_max')->default(5)->after('production_min');
            $table->string('description_template', 40)->nullable()->after('production_max');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_country', 2)->nullable()->after('shipping_postal_code');
            $table->string('currency', 3)->default('USD')->after('total');
            $table->string('locale', 5)->nullable()->after('currency');
            $table->string('payment_method')->default('card')->change();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->json('options')->nullable()->after('product_sku');
            $table->string('provider', 30)->nullable()->after('options');
        });

        Schema::create('product_reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('author', 80);
            $table->string('country', 2)->nullable();
            $table->unsignedTinyInteger('rating');
            $table->string('title', 150)->nullable();
            $table->text('body');
            $table->string('locale', 5)->default('en');
            $table->boolean('verified')->default(true);
            $table->boolean('is_sample')->default(false);
            $table->timestamps();
        });

        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('mode', 10)->default('ai');          // ai | human
            $table->string('status', 15)->default('open');      // open | waiting_agent | closed
            $table->string('visitor_name')->nullable();
            $table->string('visitor_email')->nullable();
            $table->string('locale', 5)->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chat_conversation_id')->constrained()->cascadeOnDelete();
            $table->string('sender', 10);                         // visitor | bot | agent
            $table->text('body');
            $table->timestamps();
        });

        Schema::create('newsletter_subscribers', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->string('locale', 5)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_subscribers');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
        Schema::dropIfExists('product_reviews');
        Schema::table('order_items', fn (Blueprint $t) => $t->dropColumn(['options', 'provider']));
        Schema::table('orders', fn (Blueprint $t) => $t->dropColumn(['shipping_country', 'currency', 'locale']));
        Schema::table('products', fn (Blueprint $t) => $t->dropColumn(['translations', 'product_type', 'provider', 'provider_sku', 'variants', 'tags', 'motif', 'badge', 'rating', 'reviews_count', 'production_min', 'production_max', 'description_template']));
        Schema::table('categories', fn (Blueprint $t) => $t->dropColumn(['translations', 'icon', 'image', 'sort_order']));
    }
};
