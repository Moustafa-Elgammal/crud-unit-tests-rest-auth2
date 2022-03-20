<?php

namespace App\Factories;

use App\Models\Student;

class StudentFactoryDB implements FactoryInterface
{
    public function make(\stdClass $data)
    {
        if (isset($data->id))
            $student = Student::find($data->id);
        else
            $student = new Student();

        $student->name = $data->name;
        $student->school_id = $data->school_id;

        return $student;
    }
}
