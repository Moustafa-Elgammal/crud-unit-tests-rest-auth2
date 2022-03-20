<?php

namespace App\Services\Students;

use Illuminate\Http\Request;

interface StudentsServiceInterface
{
    public function createStudent(Request $request);

    public function updateStudent(Request $request);

    public function deleteStudent($student): bool;

    public function getAllStudents(): \Illuminate\Database\Eloquent\Collection;

    public function getStudentById(Request $request): ?\Illuminate\Database\Eloquent\Model;
}
