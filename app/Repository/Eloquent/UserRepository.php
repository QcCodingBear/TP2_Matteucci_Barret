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
}

