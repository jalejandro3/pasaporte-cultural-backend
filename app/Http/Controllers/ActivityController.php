<?php

namespace App\Http\Controllers;

use App\Exceptions\InputValidationException;
use App\Services\ActivityService as ActivityServiceInterface;
use Illuminate\Contracts\Pagination\Paginator;
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
            'duration' => 'required|integer|min:1',
        ];

        $messages = [
            'duration' => 'The duration must be at least 1 hour.',
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->create($request->all()));
    }

    public function getActivityAttendance(Request $request): JsonResponse
    {
        $rules = [
            'activity_id' => 'nullable|integer|exists:activities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->getActivityAttendance($request->get('activity_id')));
    }

    /**
     * @throws InputValidationException
     */
    public function getActivityUser(Request $request): JsonResponse
    {
        $rules = [
            'search' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->getActivityUser($request->get('search')));
    }

    /**
     * @throws InputValidationException
     */
    public function getAllActivities(Request $request): Paginator
    {
        $rules = [
            'country' => 'nullable|string',
            'city' => 'nullable|string',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        $filters = $request->only('country', 'city');
        $perPage = $request->input('per_page', 10);
        $sortBy = $request->input('sort_by', 'title');
        $sortOrder = $request->input('sort_order', 'asc');

        return $this->activityService->getAllActivities($filters, $perPage, $sortBy, $sortOrder);
    }

    /**
     * @throws InputValidationException
     */
    public function getAutocompleteSearch(Request $request): JsonResponse
    {
        $rules = [
            'q' => 'nullable|string',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->getAutocompleteSearch($request->get('q')));
    }

    /**
     * @throws InputValidationException
     */
    public function getEnrolledActivities(Request $request): Paginator
    {
        $rules = [
            'per_page' => 'nullable|integer|min:1|max:100',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        $perPage = $request->input('per_page', 10);

        return $this->activityService->getEnrolledActivities($perPage, $request->bearerToken());
    }

    /**
     * @throws InputValidationException
     */
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'activity_id' => 'required|exists:activities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->activityService->register($request->get('activity_id'), $request->bearerToken()));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        return $this->success($this->activityService->show($request->bearerToken(), $id));
    }
}
