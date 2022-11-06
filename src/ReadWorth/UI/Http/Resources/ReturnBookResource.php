<?php

namespace ReadWorth\UI\Http\Resources;

class ReturnBookResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly int $bookId,
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
}
