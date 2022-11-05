<?php

namespace ReadWorth\Domain\Entities;

class Book
{
    public function __construct(
        private readonly int $id,
        private readonly int $status,
        private readonly string $title,
        private readonly string|null $description,
        private readonly string|null $imagePath,
        private readonly string|null $url,
    ) {
    }

    public function getId(): int
    {
        return $this->id;
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
