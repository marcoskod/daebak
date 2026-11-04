<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Compte administrateur de démonstration (à changer dès la mise en ligne).
        User::updateOrCreate(['email' => env('SEED_ADMIN_EMAIL', 'admin@daebak.test')], [
            'name' => 'Daebak Admin',
            'password' => Hash::make(env('SEED_ADMIN_PASSWORD', 'daebak-admin-2026')),
        ])->forceFill(['is_admin' => true, 'email_verified_at' => now()])->save();

        User::updateOrCreate(['email' => env('SEED_CLIENT_EMAIL', 'demo@daebak.test')], [
            'name' => 'Demo Customer',
            'password' => Hash::make(env('SEED_CLIENT_PASSWORD', 'daebak-demo-2026')),
        ])->forceFill(['email_verified_at' => now()])->save();

        Setting::set('payment_card_enabled', '1');
        Setting::set('payment_paypal_enabled', '1');

        $this->call([CatalogSeeder::class, ReviewSeeder::class, BlogSeeder::class]);
    }
}
