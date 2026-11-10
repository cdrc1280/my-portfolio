<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiKnowledgeBaseResource\Pages;
use App\Models\AiKnowledgeBase;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class AiKnowledgeBaseResource extends Resource
{
    protected static ?string $model = AiKnowledgeBase::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-sparkles';

    protected static \UnitEnum|string|null $navigationGroup = 'AI RAG Engine';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('topic')->required(),
                TextInput::make('title')->required(),
                TextInput::make('category')->default('General'),
                MarkdownEditor::make('content')->required()->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('topic')->searchable(),
                TextColumn::make('title')->searchable(),
                TextColumn::make('category')->sortable(),
                TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiKnowledgeBases::route('/'),
            'create' => Pages\CreateAiKnowledgeBase::route('/create'),
            'edit' => Pages\EditAiKnowledgeBase::route('/{record}/edit'),
        ];
    }
}
