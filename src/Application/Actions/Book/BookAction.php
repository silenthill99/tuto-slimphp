<?php

namespace App\Application\Actions\Book;

use App\Application\Actions\Action;
use App\Domain\Book\BookRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;

abstract class BookAction extends Action
{
    protected BookRepository $bookRepository;

    public function __construct(LoggerInterface $logger, BookRepository $bookRepository) {
        parent::__construct($logger);
        $this->bookRepository = $bookRepository;
    }
}