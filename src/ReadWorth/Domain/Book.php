<?php

namespace ReadWorth\Domain;

class Book
{
    public function __construct(
        private string $status,
        private string $title,
        private string|null $description,
        private string|null $imagePath,
        private string|null $url,
    ) {
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
