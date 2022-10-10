<?php

namespace ReadWorth\Domain;

class BookCategory
{
    public function __construct(
        private int $workspaceId,
        private string $name
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
