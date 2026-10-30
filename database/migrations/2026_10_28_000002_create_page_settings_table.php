<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('page_settings', function (Blueprint $table) {
            $table->id();
            $table->string('page_key', 40)->unique();
            $table->json('texts')->nullable();     // {"en":{"home.hero.title":"…"},"fr":{…}} — surcharges de lang/*.json
            $table->json('images')->nullable();    // {"hero":"uploads/pages/x.webp"} — surcharges des images
            $table->json('styles')->nullable();    // {"vars":{"brand":"#…"},"hero":{"background_color":"#…"}}
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('page_settings');
    }
};
