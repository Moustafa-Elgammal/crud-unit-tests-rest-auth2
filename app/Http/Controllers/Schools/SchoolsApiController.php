<?php

namespace App\Http\Controllers\Schools;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Services\Schools\SchoolsServices;
use Illuminate\Http\Request;

class SchoolsApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $schools = (new SchoolsServices())->getAllSchools();
        return response()->json(['schools' => $schools]);
    }
}
