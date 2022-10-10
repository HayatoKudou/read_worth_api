<?php

namespace ReadWorth\Infrastructure\Repository;

use ReadWorth\Infrastructure\EloquentModel\Workspace;

interface IWorkspaceRepository
{
    public function findById(int $id): Workspace|null;
}
