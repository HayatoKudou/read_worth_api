<?php

namespace ReadWorth\UI\Http\Resources;

class CreateBookResource
{
    public function __construct(
        private readonly int $workspaceId,
        private readonly string $category,
        private readonly string $title,
        private readonly string|null $description,
        private readonly string|null $image,
        private readonly string|null $url,
    ) {
    }

    public function getWorkspaceId(): int
    {
        return $this->workspaceId;
    }

    public function getCategory(): string
    {
        return $this->category;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string|null
    {
        return $this->description;
    }

    public function getImage(): string|null
    {
        return $this->image;
    }

    public function getUrl(): string|null
    {
        return $this->url;
    }
}
