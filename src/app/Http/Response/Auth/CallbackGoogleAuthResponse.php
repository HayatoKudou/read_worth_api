<?php

namespace App\Http\Response\Auth;

use App\Models\User;
use Illuminate\Http\RedirectResponse;

class CallbackGoogleAuthResponse
{
    public static function make(User $user): RedirectResponse
    {
        $param = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'apiToken' => $user->api_token,
            'isAccountManager' => $user->role->is_account_manager,
            'isBookManager' => $user->role->is_book_manager,
            'isClientManager' => $user->role->is_client_manager,
            'clientId' => $user->clients[0]->id,
            'clientName' => $user->clients[0]->name,
        ];
        $query = http_build_query($param);
        return redirect()->away(config('front.url') . "/callback-auth?{$query}");
    }
}
