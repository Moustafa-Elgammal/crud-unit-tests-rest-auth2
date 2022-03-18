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

    public function updateSchool(Request $request)
    {
        $data = new \stdClass();
        $data->id = $request->id;
        $data->name = $request->name;

        if(!$this->schoolRepository->update($this->schoolFactory, $data))
        {
            foreach ($this->schoolRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return true;
    }

    public function deleteSchool(Request $request)
    {
        if(!$this->schoolRepository->delete($this->SchoolModel, $request->id))
        {
            foreach ($this->schoolRepository->getErrors() as $error)
                $this->addError($error);

            return false;
        }

        return  true;
    }

    public function getAllSchools()
    {
        return $this->schoolRepository->getAll($this->SchoolModel);
    }

    public function getSchoolById(Request $request)
    {
        return $this->schoolRepository->getById($this->SchoolModel, $request->id);
    }
}
