<?php

namespace App\Http\Controllers;

use App\Exceptions\InputValidationException;
use App\Services\ActivityService as ActivityServiceInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ActivityController extends Controller
{
    public function __construct(private readonly ActivityServiceInterface $activityService)
    {
    }

    /**
     * @throws InputValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $rules = [
            'title' => 'required|string',
            'description' => 'required|string',
            'country' => 'required|string',
            'city' => 'required|string',
            'address' => 'required|string',
            'total_hours' => 'required|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->create($request->all()));
    }

    public function show(Request $request, string $id): JsonResponse
    {
        return $this->success($this->activityService->show($request->bearerToken(), $id));
    }
}
