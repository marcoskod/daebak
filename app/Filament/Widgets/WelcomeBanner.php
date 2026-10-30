<?php

namespace App\Filament\Widgets;

use App\Models\ChatConversation;
use App\Models\ContactMessage;
use App\Models\Order;
use Filament\Widgets\Widget;

class WelcomeBanner extends Widget
{
    protected static string $view = 'filament.widgets.welcome-banner';

    protected static ?int $sort = 0;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $hour = (int) now()->format('G');

        return [
            'greeting' => $hour < 12 ? 'Good morning' : ($hour < 18 ? 'Good afternoon' : 'Good evening'),
            'name' => explode(' ', (string) auth()->user()?->name)[0],
            'pendingOrders' => Order::where('status', 'pending')->orWhere('status', 'processing')->count(),
            'waitingChats' => ChatConversation::where('status', 'waiting_agent')->count(),
            'newMessages' => ContactMessage::where('status', 'new')->count(),
        ];
    }
}
