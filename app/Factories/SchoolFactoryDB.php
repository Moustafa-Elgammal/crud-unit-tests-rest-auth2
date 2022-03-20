<?php

namespace App\Factories;

use App\Models\School;
use Illuminate\Database\Eloquent\Model;

class SchoolFactoryDB implements FactoryInterface
{
    /** create new school
     * @param \stdClass $data
     * @return School
     */
    public function make(\stdClass $data) :Model
    {
        if (isset($data->id))
            $school = School::find($data->id);
        else
            $school = new school();

        $school->name = $data->name;
        return $school;
    }
}
