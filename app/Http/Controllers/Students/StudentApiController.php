<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;
use App\Http\Requests\Students\Api\CheckStudentExistsRequest;
use App\Http\Requests\Students\Api\CreateStudentRequest;
use App\Http\Requests\Students\Api\UpdateStudentRequest;
use App\Models\Student;
use App\Services\Schools\SchoolsServices;
use App\Services\Students\StudentApiServices;
use Illuminate\Http\Request;

class StudentApiController extends Controller
{
    protected StudentApiServices $studentService;
    protected SchoolsServices $schoolsServices;

    /**
     * init the Student service
     */
    public function __construct()
    {
        $this->studentService = new StudentApiServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $students = $this->schoolsServices->getAllSchools();
        return response()->json(['students' => $students]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param CreateStudentRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreateStudentRequest $request)
    {
        if ($this->studentService->createStudent($request))
            return response()->json(["success" => true ,'message' => 'created', 'errors' =>[]]);

        return response()->json(["success" => false ,'message' => 'errors', 'errors' =>$this->studentService->getErrors()]);
    }

    /**
     * Display the specified resource.
     *`
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Student $student)
    {
        \request()->merge(['student'=> $student->id]);
        $student = $this->studentService->getStudentById(\request());
        if (!$student)
            return response()->json(["success" => false ,'message' => 'errors', 'not found' =>$this->studentService->getErrors()]);

        return response()->json(["success" => true ,'message' => 'found','student' => $student, 'errors' =>[]]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateStudentRequest $request, Student $student)
    {
        if ($this->studentService->updateStudent($request))
            return response()->json(["success" => true ,'message' => 'updated', 'errors' =>[]]);

        return response()->json(["success" => false ,'message' => 'errors', 'errors' =>$this->studentService->getErrors()]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Student $student
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Student $student)
    {
        if ($this->studentService->deleteStudent($student))
            return response()->json(["success" => true ,'message' => 'deleted', 'errors' =>[]]);

        return response()->json(["success" => false ,'message' => 'errors', 'errors' =>$this->studentService->getErrors()]);

    }
}
