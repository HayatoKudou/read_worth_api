<?php

namespace ReadWorth\Application\UseCase\Books;

use ReadWorth\Domain\Entities\User;
use Illuminate\Support\Facades\Auth;
use ReadWorth\Domain\ValueObjects\BookId;
use ReadWorth\Infrastructure\EloquentModel;
use ReadWorth\UI\Http\Resources\ReturnBookResource;
use ReadWorth\Infrastructure\Repository\BookRepository;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use ReadWorth\Application\UseCase\Slack\SlackNotification;
use ReadWorth\Infrastructure\Repository\WorkspaceRepository;

class ReturnBook
{
    use AuthorizesRequests;

    public function __construct(
        private readonly WorkspaceRepository $workspaceRepository,
        private readonly BookRepository $bookRepository,
        private readonly StoreBookImage $storeBookImage,
        private readonly SlackNotification $slackNotification
    ) {
    }

    public function return(ReturnBookResource $resource): void
    {
        $workspace = $this->workspaceRepository->findById($resource->getWorkspaceId());
        $this->authorize('affiliation', $workspace);

        $book = EloquentModel\Book::find($resource->getBookId());
        $user = EloquentModel\User::find(Auth::id());

        $title = '書籍返却のお知らせ';
        $message = '【返却者】' . $user->name;
        $imagePath = config('app.url') . '/storage/' . $book->image_path;
        $this->slackNotification->notification($title, $message, $workspace->id, $imagePath);

        $auth = \Auth::user();
        $user = new User(id: $auth->id, name: $auth->name, email: $auth->email);
        $this->bookRepository->return(new BookId($resource->getBookId()), $user);
    }
}
