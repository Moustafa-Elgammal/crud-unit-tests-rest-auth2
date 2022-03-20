<?php
namespace App\Repositories;
use App\Factories\FactoryInterface;
use Illuminate\Database\Eloquent\Model;

interface RepositoryInterface
{
    /** create model
     * @param FactoryInterface $factory
     * @param $data
     * @return bool
     */
    public function create(FactoryInterface $factory, $data): bool;

    /** delete model
     * @param Model $model
     * @param $id
     * @return bool
     */
    public function delete(Model $model, $id): bool;

    /** get all models
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Model $model): \Illuminate\Database\Eloquent\Collection;

    /** get with pagination
     * @param Model $model
     * @param $chunk
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPagination(Model $model, $chunk):\Illuminate\Contracts\Pagination\LengthAwarePaginator;

    /** get model by id
     * @param Model $model
     * @param $id
     * @return Model
     */
    public function getById(Model $model, $id): Model;

    /** update model
     * @param FactoryInterface $factory
     * @param $data
     * @return bool
     */
    public function update(FactoryInterface $factory, $data): bool;
}
