<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Remplace l'option "card" (démo) par "fedapay" (paiement en ligne réel).
        DB::statement("UPDATE orders SET payment_method = 'cash_on_delivery' WHERE payment_method = 'card'");
        if (DB::getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('fedapay', 'cash_on_delivery') NOT NULL DEFAULT 'cash_on_delivery'");
        } else {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('payment_method')->default('cash_on_delivery')->change();
            });
        }

        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_reference')->nullable()->after('payment_status');
            $table->string('fedapay_transaction_id')->nullable()->after('payment_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_reference', 'fedapay_transaction_id']);
        });

        DB::statement("UPDATE orders SET payment_method = 'cash_on_delivery' WHERE payment_method = 'fedapay'");
        DB::statement("ALTER TABLE orders MODIFY payment_method ENUM('card', 'cash_on_delivery') NOT NULL DEFAULT 'cash_on_delivery'");
    }
};
