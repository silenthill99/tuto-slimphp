<?php

namespace App\Domain\Book;

use DateTimeImmutable;
use JsonSerializable;

class Book implements JsonSerializable
{

    public int $id;
    public string $title;
    public DateTimeImmutable $publishedAt;
    public function __construct(int $id, string $title, DateTimeImmutable $publishedAt)
    {
        $this->id = $id;
        $this->title = $title;
        $this->publishedAt = $publishedAt;
    }
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'publishedAt' => $this->publishedAt->format("Y-m-d"),
        ];
    }
}