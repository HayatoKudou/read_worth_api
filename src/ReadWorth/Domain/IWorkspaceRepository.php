<?php

namespace ReadWorth\Domain;

use ReadWorth\Infrastructure\EloquentModel\Workspace;

interface IWorkspaceRepository
{
    public function findById(int $id): Workspace|null;
}
