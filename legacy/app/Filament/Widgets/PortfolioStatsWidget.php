<?php

namespace App\Filament\Widgets;

use App\Models\AiKnowledgeBase;
use App\Models\ContactMessage;
use App\Models\Project;
use App\Models\Skill;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Production Projects', Project::count())
                ->description('Active TESDA & Web Systems')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('success'),

            Stat::make('Technical Skills', Skill::count())
                ->description('AI-Assisted, Proficient & Familiar')
                ->descriptionIcon('heroicon-m-cpu-chip')
                ->color('primary'),

            Stat::make('RAG AI Knowledge Chunks', AiKnowledgeBase::count())
                ->description('Indexed AetherAI Embeddings')
                ->descriptionIcon('heroicon-m-sparkles')
                ->color('warning'),

            Stat::make('Inbound Contact Inquiries', ContactMessage::count())
                ->description('Form submissions from recruiters')
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
        ];
    }
}
