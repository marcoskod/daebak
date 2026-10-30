<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Les deux moyens de paiement sont activés par défaut (comportement actuel conservé).
        DB::table('settings')->insert([
            ['key' => 'payment_cash_on_delivery_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
            ['key' => 'payment_fedapay_enabled', 'value' => '1', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
