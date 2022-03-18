<?php
namespace App\Repositories;
use App\Factories\FactoryInterface;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    public function create(FactoryInterface $factory, $data): bool;

    public function delete(Model $model, $id): bool;

    public function getAll(Model $model): \Illuminate\Database\Eloquent\Collection;

    public function getAllWithPagination(Model $model, $chunk):\Illuminate\Contracts\Pagination\LengthAwarePaginator;

    public function getById(Model $model, $id): Model;

    public function update(FactoryInterface $factory, $data): bool;
}
