<?php

namespace ReadWorth\Domain\ValueObjects;

class BookPurchaseApplySteps
{
    public const REFUSED = 0;
    public const NEED_ACCEPT = 1;
    public const NEED_BUY = 2;
    public const NEED_NOTIFICATION = 3;
}
