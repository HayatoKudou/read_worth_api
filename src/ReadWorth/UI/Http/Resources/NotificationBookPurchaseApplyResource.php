<?php

namespace ReadWorth\UI\Http\Resources;

class NotificationBookPurchaseApplyResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly int $bookId,
        private readonly string $title,
        private readonly string $message,
        private readonly bool $skip,
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

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function getSkip(): bool
    {
        return $this->skip;
    }
}
