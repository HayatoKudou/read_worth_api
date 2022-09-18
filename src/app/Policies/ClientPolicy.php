<?php

namespace App\Policies;

use App\Models\user;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientPolicy
{
    use HandlesAuthorization;

    public function affiliation(user $user, Workspace $workspace): Response
    {
        $userBelongsToClient = $workspace
            ->users()
            ->where('users.id', $user->id)
            ->exists();

        return $userBelongsToClient
            ? $this->allow()
            : $this->deny('ユーザは指定されたワークスペースに所属していません');
    }
}
