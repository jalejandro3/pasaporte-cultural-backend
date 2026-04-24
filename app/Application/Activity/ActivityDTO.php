<?php

namespace App\Application\Activity;

use App\Domain\Activity\Activity;

final readonly class ActivityDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description,
        public string $country,
        public string $city,
        public string $address,
        public int $totalHours,
        public ?string $verificationCode = null,
    ) {}

    public static function fromEntity(Activity $activity, ?string $verificationCode = null): self
    {
        return new self(
            $activity->getId(),
            $activity->getTitle(),
            $activity->getDescription(),
            $activity->getCountry(),
            $activity->getCity(),
            $activity->getAddress(),
            $activity->getTotalHours(),
            $verificationCode,
        );
    }
}
