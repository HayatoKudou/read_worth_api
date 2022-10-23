<?php

namespace ReadWorth\Domain\Entities;

class BookCategory
{
    public function __construct(
        private readonly string $name
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
