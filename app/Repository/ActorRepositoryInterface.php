<?php

namespace App\Repository;

interface ActorRepositoryInterface extends RepositoryInterface
{
    public function getByName($name);
    public function getByLastName($lastName);
    public function getByBirthDate($birthDate);
}
