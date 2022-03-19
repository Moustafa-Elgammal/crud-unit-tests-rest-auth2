<?php

namespace App\Http\Controllers\Schools;

use App\Http\Controllers\Controller;
use App\Http\Requests\Requests\Schools\CreateSchoolRequest;
use App\Http\Requests\Requests\Schools\UpdateSchoolRequest;
use App\Models\School;
use App\Services\Schools\SchoolsServices;
use Illuminate\Http\Request;

class SchoolsAdminController extends Controller
{
    protected SchoolsServices $schoolService;

    /**
     * init the school service
     */
    public function __construct()
    {
        $this->schoolService = new SchoolsServices();
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $schools = $this->schoolService->getAllSchools();
        return view('schools.index',['schools' => $schools]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     */
    public function store(CreateSchoolRequest $request)
    {
        if ($this->schoolService->createSchool($request))
        {
            return redirect()->back()->with('message','Created');
        }else{
            return redirect()->back()->with('service_errors' , $this->schoolService->getErrors());
        }
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\School  $school
     */
    public function edit(School $school)
    {
        return view('schools.update',['school' => $school]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  UpdateSchoolRequest  $request
     */
    public function update(UpdateSchoolRequest $request)
    {
        if ($this->schoolService->updateSchool($request))
        {
            return redirect('admin/schools')->with('message','Updated');
        }else{
            return redirect()->back()->with('service_errors' , $this->schoolService->getErrors());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\School $school
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(School $school): \Illuminate\Http\RedirectResponse
    {
        if ($this->schoolService->deleteSchool($school))
        {
            return redirect()->back()->with('message','Deleted');
        }else{
            return redirect()->back()->with('errors' , $this->schoolService->getErrors());
        }
    }
}
