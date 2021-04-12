<?php

namespace App\Http\Controllers;

use App\Models\Specialty;

class SpecialtyController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $specialties = Specialty::all();
        return $this->successfulResponse(200, $specialties);
    }
}
