<?php

namespace App\Services\Impl;

use App\Models\Activity;
use App\Models\QrCode;
use App\Repositories\ActivityRepository;
use App\Services\QrCodeService as QrCodeServiceInterface;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class QrCodeService implements QrCodeServiceInterface
{
    public const QR_CODE_PATH = 'qrcodes';
    public const QR_CODE_SIZE = 300;
    public const QR_CODE_MARGIN = 2;
    public const QR_CODE_FORMAT = 'png';
    public const QR_CODE_EXTENSION = '.png';

    public function __construct(private readonly ActivityRepository $activityRepository)
    {
    }

    public function generateCode(Activity $activity): QrCode
    {
        $content = json_encode([
            'activity_id' => $activity->id,
            'token' => $this->generateToken($activity->id),
        ]);

        $publicStorage = Storage::disk('public');

        if (!$publicStorage->exists(self::QR_CODE_PATH)) {
            $publicStorage->makeDirectory(self::QR_CODE_PATH);
        }

        $filename = uniqid() . self::QR_CODE_EXTENSION;
        $filePath = self::QR_CODE_PATH . DIRECTORY_SEPARATOR . $filename;

        QrCodeGenerator::format(self::QR_CODE_FORMAT)
            ->margin(self::QR_CODE_MARGIN)
            ->size(self::QR_CODE_SIZE)
            ->generate($content, $publicStorage->path($filePath));

        return QrCode::updateOrCreate([
            'activity_id' => $activity->id,
            'path' => 'storage' . DIRECTORY_SEPARATOR . $filePath,
        ]);
    }

    public function regenerateCode(int $activityId): array
    {
        $activity = $this->activityRepository->findById($activityId);

        if (!$activity) {
            throw new ResourceNotFoundException('Activity not found.');
        }

        if (!$activity->activeQrCode) {
            throw new ResourceNotFoundException('There is not an active qr code.');
        }

        $this->generateCode($activity);

        return ['message' => 'QR code regenerated successfully.'];
    }

    private function generateToken(int $activityId): string
    {
        $secret = config('qr.secret');
        $payload = [
            'activity_id' => $activityId,
            'timestamp' => now()->timestamp,
        ];

        $payloadString = json_encode($payload);
        $hash =  hash_hmac('sha256', $payloadString, $secret);

        return base64_encode($payloadString . '.' . $hash);
    }
}
