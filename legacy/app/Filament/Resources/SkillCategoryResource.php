<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SkillCategoryResource\Pages;
use App\Models\SkillCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class SkillCategoryResource extends Resource
{
    protected static ?string $model = SkillCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static \UnitEnum|string|null $navigationGroup = 'Portfolio Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                TextInput::make('icon')->default('code'),
                TextInput::make('order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
                TextColumn::make('order')->sortable(),
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
            'index' => Pages\ListSkillCategories::route('/'),
            'create' => Pages\CreateSkillCategory::route('/create'),
            'edit' => Pages\EditSkillCategory::route('/{record}/edit'),
        ];
    }
}
