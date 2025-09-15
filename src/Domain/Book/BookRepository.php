<?php

namespace App\Domain\Book;

interface BookRepository
{
    /**
     * @return Book[]
     */
    public function findAll(): array;

    public function findBookOfId(int $id): Book;
}