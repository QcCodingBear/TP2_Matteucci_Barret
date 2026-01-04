<?php

namespace App\Repository\Eloquent;

use App\Models\Language;
use App\Repository\LanguageRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

class LanguageRepository extends BaseRepository implements LanguageRepositoryInterface
{
    public function __construct(Language $model)
    {
        parent::__construct($model);
    }

    public function getByCode($code)
    {
        $language = $this->model->where('code', $code)->first();

        if (!$language) {
            throw new ModelNotFoundException("Language with code {$code} not found.");
        }

        return $language;
    }

    public function getByName($name)
    {
        $language = $this->model->where('name', $name)->first();

        if (!$language) {
            throw new ModelNotFoundException("Language with name {$name} not found.");
        }

        return $language;
    }
}
