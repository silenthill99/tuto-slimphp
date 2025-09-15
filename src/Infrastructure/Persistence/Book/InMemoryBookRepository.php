<?php

namespace App\Infrastructure\Persistence\Book;

use App\Domain\Book\Book;
use App\Domain\Book\BookRepository;
use DateTimeImmutable;

class InMemoryBookRepository implements BookRepository
{
    private array $books;

    public function __construct(?array $books = null) {
        $this->books = $books ?? [
            1 => new Book(1, 'The lord of the Rings', new DateTimeImmutable('1954-07-29')),
            2 => new Book(2, 'The Hobbit', new DateTimeImmutable('1937-09-21')),
            3 => new Book(3, "The Two Towers", new DateTimeImmutable('1957-07-29'))
        ];
    }

    /**
     * @inheritDoc
     */
    public function findAll(): array
    {
        return array_values($this->books);
    }

    public function findBookOfId(int $id): Book
    {
        return $this->books[$id];
    }
}