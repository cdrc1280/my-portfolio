<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Projects;

use App\Domain\Projects\Models\Project;

class UpdateProjectRequest extends StoreProjectRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->project()) ?? false;
    }

    protected function ignoredId(): ?int
    {
        return $this->project()->id;
    }

    private function project(): Project
    {
        /** @var Project */
        return $this->route('project');
    }
}
