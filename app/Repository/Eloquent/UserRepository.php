<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function getByEmail($email)
    {
        $user = $this->model->where('email', $email)->first();

        if (!$user) {
            throw new ModelNotFoundException("User with email {$email} not found.");
        }

        return $user;
    }

    public function getByLogin($login)
    {
        $user = $this->model->where('login', $login)->first();

        if (!$user) {
            throw new ModelNotFoundException("User with login {$login} not found.");
        }

        return $user;
    }

    public function getByLastName($lastName)
    {
        $user = $this->model->where('last_name', $lastName)->first();

        if (!$user) {
            throw new ModelNotFoundException("User with last name {$lastName} not found.");
        }

        return $user;
    }

    public function getByFirstName($firstName)
    {
        $user = $this->model->where('first_name', $firstName)->first();

        if (!$user) {
            throw new ModelNotFoundException("User with first name {$firstName} not found.");
        }

        return $user;
    }
}

