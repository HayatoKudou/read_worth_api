<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\Workspace;

class WorkspaceRepository implements IWorkspaceRepository
{
    public function findById(int $id): Workspace|null
    {
        return Workspace::find($id);
    }
}
