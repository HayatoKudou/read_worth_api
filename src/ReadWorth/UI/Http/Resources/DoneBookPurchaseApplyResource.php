<?php

namespace ReadWorth\UI\Http\Resources;

class DoneBookPurchaseApplyResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly int $bookId,
        private readonly string $location,
    ) {
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function getBookId(): int
    {
        return $this->bookId;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}
