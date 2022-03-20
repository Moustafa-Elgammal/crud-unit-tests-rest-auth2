<?php

namespace App\Factories;

use Illuminate\Database\Eloquent\Model;

interface FactoryInterface
{
    /** create new model
     * @param \stdClass $data
     * @return mixed
     */
    public function make(\stdClass $data): Model;
}
