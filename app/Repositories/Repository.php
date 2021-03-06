<?php

namespace App\Repositories;

use App\Errors\Errors;
use App\Factories\FactoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class Repository extends Errors implements RepositoryInterface
{
    /** create Model
     * @param FactoryInterface $factory
     * @param $data
     * @return bool
     */
    public function create(FactoryInterface $factory, $data): bool
    {
        $model = $factory->make($data);
        try {
            if($model->save())
                return  true;

            $this->addError('Can not be saved');
            return  false;
        } catch (\Exception $exception)
        {
            $this->addError($exception->getMessage());
            Log::debug($exception->getMessage());
            return  false;
        }
    }

    /** delete Model
     * @param Model $model
     * @param $id
     * @return bool
     */
    public function delete(Model $model, $id): bool
    {
        $model = $model->find($id);
        try {
            if($model->delete())
                return  true;

            $this->addError('Can not be deleted');
            return  false;

        } catch (\Exception $exception)
        {
            $this->addError($exception->getMessage());
            Log::debug($exception->getMessage());
            return  false;
        }
    }

    /** get all model
     * @param Model $model
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAll(Model $model): \Illuminate\Database\Eloquent\Collection
    {
        return $model::all();
    }

    /** pagination models
     * @param Model $model
     * @param $chunk
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllWithPagination(Model $model, $chunk = 30): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $model::query()->paginate($chunk);
    }


    /**
     * @param Model $model
     * @param $id
     * @return null
     */
    public function getById(Model $model, $id): Model
    {
        return $model->find($id);
    }

    /** update model
     * @param FactoryInterface $factory
     * @param $data
     * @return bool
     */
    public function update(FactoryInterface $factory, $data): bool
    {
        $model = $factory->make($data);

        try {

            if($model->save())
                return  true;

            $this->addError('Can not be update');
            return  false;
        } catch (\Exception $exception)
        {
            $this->addError($exception->getMessage());
            Log::debug($exception->getMessage());
            return  false;
        }
    }
}
