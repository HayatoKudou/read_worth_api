<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\Workspace;

class WorkspaceRepository
{
    public function findById(int $id): Workspace|null
    {
        return Workspace::find($id);
    }
}
