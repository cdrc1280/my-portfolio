<?php

namespace App\Filament\Widgets;

use App\Models\ContactMessage;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestInquiriesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ContactMessage::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable(),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('subject')->limit(30),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'warning' => 'unread',
                        'success' => 'read',
                        'primary' => 'replied',
                    ]),
                Tables\Columns\TextColumn::make('created_at')->dateTime(),
            ]);
    }
}
