<?php

namespace ReadWorth\Domain\Entities;

class BookPurchaseApply
{
    public function __construct(
        private readonly string $reason,
        private readonly int $price,
        private readonly int $step,
    ) {
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getStep(): int
    {
        return $this->step;
    }
}
