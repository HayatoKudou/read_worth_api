<?php

namespace ReadWorth\Domain;

class Workspace
{
    public function __construct(
        private string $name,
    ) {
    }

    public function getName(): string
    {
        return $this->name;
    }
}
