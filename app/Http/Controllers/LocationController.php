<?php

namespace App\Http\Controllers;

use App\Exceptions\ResourceNotFoundException;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;

class LocationController extends Controller
{
    public function getCountries(): JsonResponse
    {
        return $this->success(Country::all());
    }

    /**
     * @throws ResourceNotFoundException
     */
    public function getCities(int $countryId): JsonResponse
    {
        $cities = City::where('country_id', $countryId)->get(['id', 'name']);

        if ($cities->isEmpty()) {
            throw new ResourceNotFoundException('No cities found.');
        }

        return $this->success($cities);
    }
}
