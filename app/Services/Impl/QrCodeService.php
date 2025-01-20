<?php

namespace App\Services\Impl;

use App\Models\Activity;
use App\Models\QrCode;
use App\Services\QrCodeService as QrCodeServiceInterface;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode as QrCodeGenerator;

class QrCodeService implements QrCodeServiceInterface
{
    public const QR_CODE_PATH = 'qrcodes';
    public const QR_CODE_SIZE = 300;
    public const QR_CODE_FORMAT = 'png';
    public const QR_CODE_EXTENSION = '.png';

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
            ->size(self::QR_CODE_SIZE)
            ->generate($content, $publicStorage->path($filePath));

        return QrCode::create([
            'activity_id' => $activity->id,
            'path' => 'storage' . DIRECTORY_SEPARATOR . $filePath,
        ]);
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
