<?php

namespace App\Application\Actions\Book;

use App\Application\Actions\Book\BookAction;
use Psr\Http\Message\ResponseInterface as Response;

class ListBooksAction extends BookAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
       $books = $this->bookRepository->findAll();
       $this->logger->info("Book list was viewed");
       return $this->respondWithData($books);
    }
}