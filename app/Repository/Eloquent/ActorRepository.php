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
}
