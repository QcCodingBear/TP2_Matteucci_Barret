<?php

namespace App\Repository;

interface UserRepositoryInterface extends RepositoryInterface
{
    public function getByEmail($email);
    public function getByLogin($login);
    public function getByLastName($lastName);
    public function getByFirstName($firstName);
    public function updatePassword($id, $newPassword);
}
