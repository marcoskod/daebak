<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ShopSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?string $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Shop settings';

    protected static ?string $title = 'Shop settings';

    protected static string $view = 'filament.pages.shop-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'payment_card_enabled' => Setting::getBool('payment_card_enabled'),
            'payment_paypal_enabled' => Setting::getBool('payment_paypal_enabled'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Payment methods')
                    ->description('At least one payment method must stay active, otherwise checkout is blocked for your customers. Payments run in demo mode until a Stripe / PayPal key is configured in .env.')
                    ->schema([
                        Toggle::make('payment_card_enabled')->label('Credit / debit card')->helperText('Visa, Mastercard, Amex, Apple Pay, Google Pay.')->default(true),
                        Toggle::make('payment_paypal_enabled')->label('PayPal')->default(true),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        if (! $state['payment_card_enabled'] && ! $state['payment_paypal_enabled']) {
            Notification::make()->title('Cannot disable everything')->body('At least one payment method must stay active.')->danger()->send();

            return;
        }

        Setting::set('payment_card_enabled', $state['payment_card_enabled'] ? '1' : '0');
        Setting::set('payment_paypal_enabled', $state['payment_paypal_enabled'] ? '1' : '0');

        Notification::make()->title('Settings saved')->success()->send();
    }
}
