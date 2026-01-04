<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repository\ActorRepositoryInterface;

class ActorController extends Controller
{
    private ActorRepositoryInterface $actorRepository;

    public function __construct(ActorRepositoryInterface $actorRepository)
    {
        $this->actorRepository = $actorRepository;
    }

    public function getById($id)
    {
        $actor = $this->actorRepository->getById($id);
        return response()->json($actor);
    }

}
