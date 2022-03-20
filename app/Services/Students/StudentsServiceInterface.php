<?php

namespace App\Services\Students;

use Illuminate\Http\Request;

interface StudentsServiceInterface
{
    /** create student
     * @param Request $request
     * @return mixed
     */
    public function createStudent(Request $request);

    /** update student
     * @param Request $request
     * @return mixed
     */
    public function updateStudent(Request $request);

    /** delete student
     * @param $student
     * @return bool
     */
    public function deleteStudent($student): bool;

    /** get students
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStudents(): \Illuminate\Database\Eloquent\Collection;

    /** get student by id
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getStudentById(Request $request): ?\Illuminate\Database\Eloquent\Model;
}
