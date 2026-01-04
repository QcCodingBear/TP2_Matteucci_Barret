<?php
namespace App\Repository;

use App\Http\Controllers\Controller;

interface RepositoryInterface
{
public function create(array $data);
public function getAll($perPage = SEARCH_PAGINATION);
public function getById($id);
public function update($id, array $data);
public function delete($id);
}
