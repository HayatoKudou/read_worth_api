<?php

namespace ReadWorth\UI\Http\Resources;

class DeleteBookResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly array $bookIds,
    ) {
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function getBookIds(): array
    {
        return $this->bookIds;
    }
}
