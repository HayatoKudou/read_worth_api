<?php

namespace ReadWorth\UI\Http\Resources;

class CreateBookCategoryResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly string $name,
    ) {
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function getName(): string
    {
        return $this->name;
    }
}
