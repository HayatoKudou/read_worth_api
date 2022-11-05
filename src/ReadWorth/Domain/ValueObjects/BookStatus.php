<?php

namespace ReadWorth\Domain\ValueObjects;

class BookStatus
{
    public const STATUS_CAN_LEND = 1;
    public const STATUS_CAN_NOT_LEND = 2;
    public const STATUS_APPLYING = 3;
}
