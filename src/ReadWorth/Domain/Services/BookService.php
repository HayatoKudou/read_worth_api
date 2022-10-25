<?php

namespace ReadWorth\Domain\Services;

use ReadWorth\Domain\IBookRepository;
use ReadWorth\Domain\ValueObjects\BookStatus;

class BookService
{
    public function __construct(private readonly IBookRepository $bookRepository)
    {
    }

    public function updateAction(int $bookId, $status): string|null
    {
        $book = $this->bookRepository->findById($bookId);

        if ($book->status != $status) {
            // 申請中 ⇨ 登録
            if (BookStatus::STATUS_APPLYING === $book->status && BookStatus::STATUS_CAN_LEND === $status) {
                return 'create book';
            // 貸出中 ⇨ 貸出可能
            } elseif (BookStatus::STATUS_CAN_NOT_LEND === $book->status && BookStatus::STATUS_CAN_LEND === $status) {
                return 'return book';
            // 貸出可能 ⇨ 貸出中
            } elseif (BookStatus::STATUS_CAN_LEND === $book->status && BookStatus::STATUS_CAN_NOT_LEND === $status) {
                return 'lend book';
            }
        }
        return null;
    }
}
