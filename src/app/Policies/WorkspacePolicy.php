<?php

namespace App\Policies;

use App\Models\user;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class WorkspacePolicy
{
    use HandlesAuthorization;

    public function affiliation(user $user, Workspace $workspace): Response
    {
        $userBelongsToWorkSpace = $workspace
            ->users()
            ->where('users.id', $user->id)
            ->exists();

        return $userBelongsToWorkSpace
            ? $this->allow()
            : $this->deny('ユーザは指定されたワークスペースに所属していません');
    }

    public function isAccountManager(user $user, Workspace $workspace): Response
    {
        $isAccountManager = $workspace->roles()
            ->where('belongings.user_id', $user->id)
            ->value('roles.is_account_manager');

        return $isAccountManager
            ? $this->allow()
            : $this->deny('ユーザはアカウント管理権限がありません');
    }

    public function isBookManager(user $user, Workspace $workspace): Response
    {
        $isBookManager = $workspace->roles()
            ->where('belongings.user_id', $user->id)
            ->value('roles.is_book_manager');

        return $isBookManager
            ? $this->allow()
            : $this->deny('ユーザは書籍管理権限がありません');
    }

    public function isWorkspaceManager(user $user, Workspace $workspace): Response
    {
        $isBookManager = $workspace->roles()
            ->where('belongings.user_id', $user->id)
            ->value('roles.is_workspace_manager');

        return $isBookManager
            ? $this->allow()
            : $this->deny('ユーザはワークスペース管理権限がありません');
    }
}
