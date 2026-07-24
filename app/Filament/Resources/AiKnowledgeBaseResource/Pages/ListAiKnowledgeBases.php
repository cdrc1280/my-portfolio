<?php

namespace App\Filament\Resources\AiKnowledgeBaseResource\Pages;

use App\Filament\Resources\AiKnowledgeBaseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAiKnowledgeBases extends ListRecords
{
    protected static string $resource = AiKnowledgeBaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
