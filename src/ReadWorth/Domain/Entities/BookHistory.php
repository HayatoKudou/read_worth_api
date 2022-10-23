<?php

namespace ReadWorth\Domain;

class BookHistory
{
    public function __construct(
        private int $workspaceId,
        private int $bookCategoryId,
        private string $status,
        private string $title,
        private string|null $description,
        private string|null $imagePath,
        private string|null $url,
    ) {
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function getBookCategoryId(): int
    {
        return $this->bookCategoryId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function getImagePath(): string|null
    {
        return $this->imagePath;
    }

    public function getUrl(): string|null
    {
        return $this->url;
    }
}
