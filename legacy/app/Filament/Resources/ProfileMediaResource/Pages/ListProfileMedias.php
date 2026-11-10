<?php

namespace App\Filament\Resources\ProfileMediaResource\Pages;

use App\Filament\Resources\ProfileMediaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProfileMedias extends ListRecords
{
    protected static string $resource = ProfileMediaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
