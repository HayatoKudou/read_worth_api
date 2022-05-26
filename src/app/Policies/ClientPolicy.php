<?php

namespace App\Policies;

use App\Models\user;
use App\Models\Client;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function affiliation(user $user, Client $client): Response
    {
        $userBelongsToClient = $client
            ->users()
            ->where('id', $user->id)
            ->exists();

        return $userBelongsToClient
            ? $this->allow()
            : $this->deny('ユーザは指定された組織に所属していません');
    }
}
