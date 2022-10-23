<?php

namespace ReadWorth\Domain\ValueObjects;

class WorkspaceName
{
    public function __construct(
        private readonly string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
