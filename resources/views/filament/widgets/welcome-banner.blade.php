<x-filament-widgets::widget>
    <div class="dbk-welcome">
        <div class="dbk-welcome-copy">
            <span class="dbk-eyebrow">{{ config('site.brand.name') }} · {{ config('site.brand.native_name') }}</span>
            <h2>{{ $greeting }}, {{ $name }} 👋</h2>
            <p>
                {{ $pendingOrders }} {{ \Illuminate\Support\Str::plural('order', $pendingOrders) }} to prepare ·
                {{ $waitingChats }} {{ \Illuminate\Support\Str::plural('chat', $waitingChats) }} waiting ·
                {{ $newMessages }} new {{ \Illuminate\Support\Str::plural('message', $newMessages) }}
            </p>
            <div class="dbk-welcome-actions">
                <a href="{{ \App\Filament\Resources\ProductResource::getUrl('create') }}" class="dbk-pill dbk-pill-solid"><x-filament::icon icon="heroicon-m-plus" class="h-4 w-4" /> New product</a>
                <a href="{{ \App\Filament\Resources\BlogPostResource::getUrl('create') }}" class="dbk-pill"><x-filament::icon icon="heroicon-m-pencil-square" class="h-4 w-4" /> Write an article</a>
                <a href="{{ \App\Filament\Resources\OrderResource::getUrl() }}" class="dbk-pill"><x-filament::icon icon="heroicon-m-shopping-bag" class="h-4 w-4" /> Orders</a>
                <a href="{{ \App\Filament\Resources\ChatConversationResource::getUrl() }}" class="dbk-pill"><x-filament::icon icon="heroicon-m-chat-bubble-left-right" class="h-4 w-4" /> Live chat</a>
                <a href="{{ url('/') }}" target="_blank" class="dbk-pill"><x-filament::icon icon="heroicon-m-arrow-top-right-on-square" class="h-4 w-4" /> View shop</a>
            </div>
        </div>
        <img class="dbk-welcome-art" src="{{ asset('images/brand/hero-collage.svg') }}" alt="" width="360" height="256">
    </div>
</x-filament-widgets::widget>
