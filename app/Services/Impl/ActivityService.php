<?php

namespace App\Services\Impl;

use App\Enums\ActivityStatus;
use App\Enums\UserRoles;
use App\Exceptions\ApplicationException;
use App\Models\Activity;
use App\Models\User;
use App\Repositories\UserRepository as UserRepositoryInterface;
use App\Services\ActivityService as ActivityServiceInterface;
use App\Services\QrCodeService as QrCodeServiceInterface;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;
use App\Workflows\ActivityWorkflow;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class ActivityService implements ActivityServiceInterface
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly QrCodeServiceInterface $qrCodeService,
        private readonly UserRepositoryInterface $userRepository
    )
    {
    }

    public function create(array $data): array
    {
        return DB::transaction(function() use ($data) {
            $activity = $this->activityRepository->create($data);

            $this->qrCodeService->generateCode($activity);

            return ['message' => 'Activity created successfully.'];
        });
    }

    public function getActivityAttendance(?int $activityId): array
    {
        $activity = $this->activityRepository->findById($activityId);

        if (!$activity instanceof Activity) {
            throw new ResourceNotFoundException('Activity not found.');
        }

        $users = $this->userRepository->findByActivity($activity->id);

        return [
            'activity' => ['title' => $activity->title,],
            'users' => $users->isEmpty() ? [] : $users->toArray(),
        ];
    }

    public function getActivityUser(?string $search): array
    {
        $user = $this->userRepository->findBySearchTerm($search);

        if (!$user instanceof User) {
            throw new ResourceNotFoundException('User not found.');
        }

        $activities = $this->activityRepository->findByUser($user->id);

        return [
            'user' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'id_document' => $user->id_document,
            ],
            'activities' => $activities->isEmpty() ? [] : $activities->toArray(),
        ];
    }

    public function getAllActivities(array $filters, int $perPage, string $sortBy, string $sortOrder): Paginator
    {
        return $this->activityRepository->findByFilters($filters, $perPage, $sortBy, $sortOrder);
    }

    public function getAutocompleteSearch(?string $q): array
    {
        return $this->activityRepository->findByQuery($q)->toArray();
    }

    public function getEnrolledActivities(int $perPage, string $token): Paginator
    {
        $decoded = jwt_decode_token($token);
        $user = $this->userRepository->findById($decoded->data->id);

        if (!$user) {
            throw new ResourceNotFoundException('User not found.');
        }

        return $this->activityRepository->findEnrolledByUser($perPage, $user->id);
    }

    public function show(string $token, int $id): array
    {
        $decoded = jwt_decode_token($token);

        $activity = $this->activityRepository->findById($id);
        $data = $activity->toArray();

        if (UserRoles::ADMIN->value === $decoded->data->role) {
            $data['qr_code_url'] = $activity->activeQrCode->url ?? null;
        }

        return $data;
    }

    /**
     * @throws ApplicationException
     */
    public function register(int $activityId, string $token): array
    {
        $decoded = jwt_decode_token($token);
        $user = $this->userRepository->findById($decoded->data->id);

        $activity = $user->activities()->where('activity_id', $activityId)->first();
        $pivotData = $activity?->pivot;

        $msg = [];

        if (!$pivotData) {
            $user->activities()->attach($activityId, ['started_at' => now()]);

            $msg['message'] = 'Activity registered successfully.';
        }

        if (in_array($pivotData->status, [ActivityStatus::COMPLETED->value, ActivityStatus::NOT_COMPLETED->value])) {
            throw new ApplicationException('You cannot scan this activity again. You have already completed it.');
        }

        if ($pivotData->started_at && !$pivotData->finished_at) {
            $newStatus = $this->determineCompletionStatus(
                $pivotData->started_at,
                now()->format('Y-m-d H:i:s'),
                $activity->duration
            );

            ActivityWorkflow::ensureTransitionIsValid(ActivityStatus::IN_PROGRESS->value, $newStatus);

            $user->activities()->updateExistingPivot($activityId, [
                'finished_at' => now(),
                'status' => $newStatus,
            ]);

            $msg['message'] = 'Activity finished successfully.';
        }

        return $msg;
    }

    private function determineCompletionStatus(string $startedAt, string $finishedAt, string $activityDuration): string
    {
        $requiredDuration = $activityDuration * 3600;
        $actualDuration = strtotime($finishedAt) - strtotime($startedAt);

        return $actualDuration >= $requiredDuration ? ActivityStatus::COMPLETED->value : ActivityStatus::NOT_COMPLETED->value;
    }
}
