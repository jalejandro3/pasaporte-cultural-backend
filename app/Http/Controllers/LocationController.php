<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LocationController extends Controller
{
    public function getCountries(): JsonResponse
    {
        return $this->success(Country::all());
    }

    public function getCities(int $countryId): JsonResponse
    {
        $cities = City::where('country_id', $countryId)->get(['id', 'name']);

        if ($cities->isEmpty()) {
            return response()->json(['message' => 'No cities found.'], Response::HTTP_NOT_FOUND);
        }

        return $this->success($cities);
    }
}
