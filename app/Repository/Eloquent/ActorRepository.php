<?php

namespace App\Repository\Eloquent;

use App\Models\Actor;
use App\Repository\ActorRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

class ActorRepository extends BaseRepository implements ActorRepositoryInterface
{
    public function __construct(Actor $model)
    {
        parent::__construct($model);
    }

    public function getByName($name)
    {
        $actor = $this->model->where('name', $name)->first();

        if (!$actor) {
            throw new ModelNotFoundException("Actor with name {$name} not found.");
        }

        return $actor;
    }

    public function getByLastName($lastName)
    {
        $actor = $this->model->where('last_name', $lastName)->first();

        if (!$actor) {
            throw new ModelNotFoundException("Actor with last name {$lastName} not found.");
        }

        return $actor;
    }

    public function getByBirthDate($birthDate)
    {
        $actor = $this->model->where('birth_date', $birthDate)->first();

        if (!$actor) {
            throw new ModelNotFoundException("Actor with birth date {$birthDate} not found.");
        }

        return $actor;
    }
}
