<?php

namespace App\Repository;

interface RepositoryInterface
{
public function create(array $data);
public function getAll($perPage = 0);
public function getById($id);
public function update($id, array $data);
public function delete($id);
}
