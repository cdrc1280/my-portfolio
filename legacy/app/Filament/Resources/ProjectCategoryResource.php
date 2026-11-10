<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectCategoryResource\Pages;
use App\Models\ProjectCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProjectCategoryResource extends Resource
{
    protected static ?string $model = ProjectCategory::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-folder';

    protected static \UnitEnum|string|null $navigationGroup = 'Portfolio Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->required(),
                TextInput::make('slug')->required()->unique(ignoreRecord: true),
                ColorPicker::make('color')->default('#3b82f6'),
                TextInput::make('order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('slug'),
                ColorColumn::make('color'),
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
            'index' => Pages\ListProjectCategories::route('/'),
            'create' => Pages\CreateProjectCategory::route('/create'),
            'edit' => Pages\EditProjectCategory::route('/{record}/edit'),
        ];
    }
}
