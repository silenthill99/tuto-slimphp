<?php

namespace App\Application\Actions\Book;

use App\Application\Actions\Book\BookAction;
use App\Domain\Book\Book;
use Psr\Http\Message\ResponseInterface as Response;

class ViewBookAction extends BookAction
{

    /**
     * @inheritDoc
     */
    protected function action(): Response
    {
        $bookId = $this->resolveArg('id');
        $book = $this->bookRepository->findBookOfId($bookId);
        $this->logger->info("Book of id `$bookId` was viewed.");
        return $this->respondWithData($book);
    }
}