<?php

namespace ReadWorth\UI\Http\Responders;

use Illuminate\Http\RedirectResponse;
use ReadWorth\Infrastructure\EloquentModel\User;

class CallbackGoogleAuthResponse
{
    public static function make(User $user): RedirectResponse
    {
        $workspace = $user->workspaces[0];
        $param = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'apiToken' => $user->api_token,
            'isAccountManager' => $user->role($workspace->id)->is_account_manager,
            'isBookManager' => $user->role($workspace->id)->is_book_manager,
            'isWorkspaceManager' => $user->role($workspace->id)->is_workspace_manager,
            'workspaceId' => $workspace->id,
            'clientName' => $workspace->name,
        ];
        $query = http_build_query($param);
        return redirect()->away(config('front.url') . "/callback-auth?{$query}");
    }
}
