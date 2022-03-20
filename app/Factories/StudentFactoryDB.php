<?php

namespace App\Factories;

use App\Models\Student;
use Illuminate\Database\Eloquent\Model;

class StudentFactoryDB implements FactoryInterface
{
    /** create new students
     * @param \stdClass $data
     * @return Student
     */
    public function make(\stdClass $data) :Model
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
