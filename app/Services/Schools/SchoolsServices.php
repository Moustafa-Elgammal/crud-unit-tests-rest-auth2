<?php

namespace App\Services\Schools;

use App\Factories\SchoolFactoryDB;
use App\Models\School;
use App\Repositories\Schools\SchoolsRepository;
use App\Services\Services;
use Illuminate\Http\Request;

class SchoolsServices extends Services
{
    private SchoolsRepository $schoolRepository;
    private School $SchoolModel;
    private SchoolFactoryDB $schoolFactory;

    public function __construct()
    {
        $this->schoolRepository = new SchoolsRepository();
        $this->schoolFactory = new SchoolFactoryDB();
        $this->SchoolModel = new School();
    }

    /** create school
     * @param Request $request
     * @return bool
     */
    public function createSchool(Request $request)
    {
        $data = new \stdClass();
        $data->name = $request->name;

        if(!$this->schoolRepository->create($this->schoolFactory, $data))
        {
            foreach ($this->schoolRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return true;
    }

    /** update school
     * @param Request $request
     * @return bool
     */
    public function updateSchool(Request $request)
    {
        $data = new \stdClass();

        $data->id = $request->school;
        $data->name = $request->name;

        if(!$this->schoolRepository->update($this->schoolFactory, $data))
        {
            foreach ($this->schoolRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return true;
    }

    /** Delete School
     * @param $school
     * @return bool
     */
    public function deleteSchool($school): bool
    {
        if(!$this->schoolRepository->delete($this->SchoolModel, $school->id))
        {
            foreach ($this->schoolRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return  true;
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAllSchools(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->schoolRepository->getAll($this->SchoolModel);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function getSchoolById(Request $request): ?\Illuminate\Database\Eloquent\Model
    {
        return $this->schoolRepository->getById($this->SchoolModel, $request->school);
    }
}
