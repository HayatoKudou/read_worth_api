<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Client;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;

class ClientPolicy
{
    use HandlesAuthorization;

    public function store(Client $client): Response
    {
        $userBelongsToClient = $client
            ->users()
            ->exists();

        return $userBelongsToClient
            ? $this->allow()
            : $this->deny('ユーザは指定された組織に所属していません');
    }
}
