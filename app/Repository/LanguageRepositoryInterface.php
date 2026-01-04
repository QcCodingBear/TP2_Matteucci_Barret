<?php

namespace App\Repository;

interface LanguageRepositoryInterface extends RepositoryInterface
{
    public function getByCode($code);
    public function getByName($name);
}
