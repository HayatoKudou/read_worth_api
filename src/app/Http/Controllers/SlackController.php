<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Contracts\View\View;
use ReadWorth\Application\UseCase\CallbackSlack;

class SlackController extends Controller
{
    public function __construct(
        private readonly CallbackSlack $callbackSlack
    ) {
    }

    public function connect(string $workspaceId): JsonResponse
    {
        \Cache::put('slack_connect_' . \Auth::id(), $workspaceId);
        return response()->json();
    }

    public function callback(Request $request): View
    {
        if ($request->has('error')) {
            \Log::error('Slack連携中にエラーが発生しました', [var_export($request->all())]);
            return view('slack_authed')->with('message', 'Slack連携中にエラーが発生しました。時間を空け再度お試しください。');
        }
        return $this->callbackSlack->callback($request->get('code'));
    }
}
