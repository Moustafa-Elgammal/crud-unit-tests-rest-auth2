<?php

namespace App\Services\Students;

use App\Factories\StudentFactoryDB;
use App\Models\Student;
use App\Repositories\Students\StudentsRepository;
use App\Services\Services;
use Illuminate\Http\Request;

class StudentsServices extends Services implements StudentsServiceInterface
{
    private StudentsRepository $studentRepository;
    private Student $studentModel;
    private StudentFactoryDB $studentFactory;

    /**
     * init all need  factories, repository and model
     */
    public function __construct()
    {
        $this->studentRepository = new StudentsRepository();
        $this->studentFactory = new StudentFactoryDB();
        $this->studentModel = new Student();
    }

    /** create Student
     * @param Request $request
     * @return bool
     */
    public function createStudent(Request $request)
    {
        $data = new \stdClass();
        $data->name = $request->name;
        $data->school_id = $request->school_id;

        if(!$this->studentRepository->create($this->studentFactory, $data))
        {
            foreach ($this->studentRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return true;
    }

    /** update Student
     * @param Request $request
     * @return bool
     */
    public function updateStudent(Request $request)
    {
        $data = new \stdClass();

        $data->id = $request->student;
        $data->name = $request->name;
        $data->school_id = $request->school_id;

        if(!$this->studentRepository->update($this->studentFactory, $data))
        {
            foreach ($this->studentRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return true;
    }

    /** Delete Student
     * @param $student
     * @return bool
     */
    public function deleteStudent($student): bool
    {
        if(!$this->studentRepository->delete($this->studentModel, $student->id))
        {
            foreach ($this->studentRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return  true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllStudents(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->studentRepository->getAll($this->studentModel);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getStudentById(Request $request): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->studentRepository->getById($this->studentModel, $request->student);
    }
}
