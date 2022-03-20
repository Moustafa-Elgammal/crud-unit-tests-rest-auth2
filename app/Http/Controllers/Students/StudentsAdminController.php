<?php

namespace App\Http\Controllers\Students;

use App\Http\Controllers\Controller;

use App\Http\Requests\Students\CreateStudentRequest;
use App\Http\Requests\Students\UpdateStudentRequest;
use App\Models\Student;
use App\Services\Schools\SchoolsServices;
use App\Services\Students\StudentsServices;

class StudentsAdminController extends Controller
{
    protected StudentsServices $studentService;
    protected SchoolsServices $schoolsServices;

    /**
     * init the Student service
     */
    public function __construct()
    {
        $this->studentService = new StudentsServices();
        $this->schoolsServices = new SchoolsServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $schools = $this->schoolsServices->getAllSchools();
        $students = $this->studentService->getAllStudents();
        return view('students.index',['students' => $students, 'schools' => $schools]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param CreateStudentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateStudentRequest $request)
    {
        if ($this->studentService->createStudent($request))
        {
            return redirect()->back()->with('message','Created');
        }else{
            return redirect()->back()->with('service_errors' , $this->studentService->getErrors());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param Student $student
     */
    public function edit(Student $student)
    {
        $schools = $this->schoolsServices->getAllSchools();
        return view('students.update',['student' => $student, 'schools'=> $schools]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateStudentRequest $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(UpdateStudentRequest $request)
    {
        if ($this->studentService->updateStudent($request))
        {
            return redirect('admin/students')->with('message','Updated');
        }else{
            return redirect()->back()->with('service_errors' , $this->studentService->getErrors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Student $student
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Student $student): \Illuminate\Http\RedirectResponse
    {
        if ($this->studentService->deleteStudent($student))
        {
            return redirect()->back()->with('message','Deleted');
        }else{
            return redirect()->back()->with('service_errors' , $this->studentService->getErrors());
        }
    }
}
