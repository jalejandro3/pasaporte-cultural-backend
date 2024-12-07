<?php

namespace App\Services\Impl;

use App\Models\User;
use App\Services\ActivityService as ActivityServiceInterface;
use App\Services\QrCodeService as QrCodeServiceInterface;
use App\Repositories\ActivityRepository as ActivityRepositoryInterface;
use Illuminate\Support\Facades\DB;

class ActivityService implements ActivityServiceInterface
{
    public function __construct(
        private readonly ActivityRepositoryInterface $activityRepository,
        private readonly QrCodeServiceInterface $qrCodeService
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

    public function show(string $token, string $id): array
    {
        $decoded = jwt_decode_token($token);

        $activity = $this->activityRepository->findById($id);
        $data = $activity->toArray();

        if (User::ROLE_ADMIN === $decoded->data->role) {
            $data['qr_code_url'] = $activity->activeQrCode->url ?? null;
        }

        return $data;
    }
}
