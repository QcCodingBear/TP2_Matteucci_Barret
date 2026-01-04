<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Critic;
use App\Http\Resources\CriticResource;
use App\Repository\CriticRepositoryInterface;

class CriticController extends Controller
{
   private CriticRepositoryInterface $criticRepository;

    public function __construct(CriticRepositoryInterface $criticRepository)
    {
         $this->criticRepository = $criticRepository;
    }

    public function getById($id)
    {
        $critic = $this->criticRepository->getById($id);
        return new CriticResource($critic);
    }
}
