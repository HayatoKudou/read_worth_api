<?php

namespace ReadWorth\UI\Http\Controllers;

use Illuminate\Http\JsonResponse;
use ReadWorth\UI\Http\Requests\BookRentalApplyRequest;
use ReadWorth\Application\UseCase\BookRentalApplies\BookRentalApply;

class BookRentalApplyController extends Controller
{
    public function __construct(
        private readonly BookRentalApply $bookRentalApply,
    ) {
    }

    public function rentalApply(string $workspaceId, string $bookId, BookRentalApplyRequest $request): JsonResponse
    {
        $validated = $request->validated();
        return $this->bookRentalApply->rentalApply($workspaceId, $bookId, $validated['reason'], $validated['expected_return_date']);
    }
}
