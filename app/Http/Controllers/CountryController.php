<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PragmaRX\Countries\Package\Countries;

class CountryController extends Controller
{
    /**
     * Retrieve countries data
     *
     * @return void
     */
    public function getCountries()
    {
        $countries = new Countries();

        $all = $countries->all()->pluck('name.common');

        return response()->json($all);
    }

    /**
     * Retrieve states data
     *
     */
    public function getStates(Request $request)
    {
        $countries = new Countries();

        $all = $countries->where('name.common', $request->name)
        ->first()
        ->hydrateStates()
        ->states
        ->sortBy('name')
        ->pluck('name');

        return response()->json($all);
    }

}
