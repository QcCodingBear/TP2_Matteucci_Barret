<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\LanguageResource;
use App\Models\Language;
use App\Repository\LanguageRepositoryInterface;

class LanguageController extends Controller
{
    private LanguageRepositoryInterface $languageRepository;

    public function __construct(LanguageRepositoryInterface $languageRepository)
    {
        $this->languageRepository = $languageRepository;
    }
}
