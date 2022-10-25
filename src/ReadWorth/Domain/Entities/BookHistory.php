<?php

namespace ReadWorth\Domain\Entities;

class BookHistory
{
    public function __construct(
        private string $action,
    ) {
    }

    public function getAction(): string
    {
        return $this->action;
    }
}
