<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProfileMediaResource\Pages;
use App\Models\ProfileMedia;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ProfileMediaResource extends Resource
{
    protected static ?string $model = ProfileMedia::class;

    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-camera';

    protected static \UnitEnum|string|null $navigationGroup = 'Site Management';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Forms\Components\TextInput::make('title')->required()->maxLength(255),
                Forms\Components\TextInput::make('caption')->maxLength(255),
                Forms\Components\FileUpload::make('image')
                    ->image()
                    ->directory('profile')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Toggle::make('is_active')->default(true),
                Forms\Components\TextInput::make('order')->numeric()->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image'),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('caption'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->dateTime(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProfileMedias::route('/'),
            'create' => Pages\CreateProfileMedia::route('/create'),
            'edit' => Pages\EditProfileMedia::route('/{record}/edit'),
        ];
    }
}
