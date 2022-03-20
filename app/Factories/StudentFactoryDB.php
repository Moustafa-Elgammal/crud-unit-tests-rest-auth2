<?php

namespace App\Factories;

use App\Models\School;

class SchoolFactoryDB implements FactoryInterface
{
    public function make(\stdClass $data)
    {
        if (isset($data->id))
            $school = School::find($data->id);
        else
            $school = new school();

        $school->name = $data->name;
        return $school;
    }
}
