<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChatConversationResource\Pages;
use App\Models\ChatConversation;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

/** Conversations du chat client : réponse manuelle de l'équipe (mode « Parler à un humain »). */
class ChatConversationResource extends Resource
{
    protected static ?string $model = ChatConversation::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Customers';

    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Live chat';

    protected static ?string $modelLabel = 'conversation';

    public static function getNavigationBadge(): ?string
    {
        $n = ChatConversation::where('status', 'waiting_agent')->count();

        return $n ? (string) $n : null;
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            TextEntry::make('visitor_name')->label('Visitor')->placeholder('Anonymous'),
            TextEntry::make('visitor_email')->label('Email')->placeholder('—'),
            TextEntry::make('mode')->badge(),
            TextEntry::make('status')->badge()->color(fn (string $state) => $state === 'waiting_agent' ? 'danger' : 'gray'),
            TextEntry::make('transcript')->label('Conversation')->columnSpanFull()->state(function (ChatConversation $r) {
                return new HtmlString($r->messages()->orderBy('id')->get()->map(function ($m) {
                    $who = ['visitor' => 'Visitor', 'bot' => 'Assistant', 'agent' => 'Team'][$m->sender] ?? $m->sender;

                    return '<div style="margin-bottom:.5rem"><b>'.e($who).'</b> <small>'.$m->created_at->format('H:i').'</small><br>'.nl2br(e($m->body)).'</div>';
                })->implode(''));
            }),
        ])->columns(4);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('last_message_at', 'desc')
            ->poll('10s')
            ->columns([
                Tables\Columns\TextColumn::make('visitor_name')->label('Visitor')->placeholder('Anonymous')->description(fn (ChatConversation $r) => $r->visitor_email),
                Tables\Columns\TextColumn::make('mode')->badge(),
                Tables\Columns\TextColumn::make('status')->badge()->color(fn (string $state) => match ($state) { 'waiting_agent' => 'danger', 'closed' => 'gray', default => 'info' }),
                Tables\Columns\TextColumn::make('messages_count')->counts('messages')->label('Msgs'),
                Tables\Columns\TextColumn::make('last_message_at')->since()->sortable(),
            ])
            ->filters([Tables\Filters\SelectFilter::make('status')->options(['open' => 'Open', 'waiting_agent' => 'Waiting for team', 'closed' => 'Closed'])])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('reply')->label('Reply')->icon('heroicon-o-paper-airplane')
                    ->form([Forms\Components\Textarea::make('body')->required()->maxLength(600)->rows(3)])
                    ->action(function (ChatConversation $record, array $data) {
                        $record->messages()->create(['sender' => 'agent', 'body' => $data['body']]);
                        $record->update(['status' => 'open', 'mode' => 'human', 'last_message_at' => now()]);
                        Notification::make()->title('Reply sent')->success()->send();
                    }),
                Tables\Actions\Action::make('close')->label('Close')->icon('heroicon-o-check')->color('gray')
                    ->action(fn (ChatConversation $record) => $record->update(['status' => 'closed'])),
            ]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ListChatConversations::route('/'), 'view' => Pages\ViewChatConversation::route('/{record}')];
    }
}
