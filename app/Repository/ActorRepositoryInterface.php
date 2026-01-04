<?php

namespace App\Repository;

interface ActorRepositoryInterface extends RepositoryInterface
{
    public function getByName($name);
}
