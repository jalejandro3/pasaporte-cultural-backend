<?php

namespace App\Domain\Activity;

use Ramsey\Uuid\Uuid;

class Activity
{
    private string $id;
    private string $verificationCode;

    private function __construct(
        private readonly string $title,
        private readonly string $description,
        private readonly string $country,
        private readonly string $city,
        private readonly string $address,
        private readonly int $totalHours,
    ) {}

    public static function create(string $title, string $description, string $country, string $city, string $address, int $totalHours): self
    {
        $activity = new Activity($title, $description, $country, $city, $address, $totalHours);

        $activity->generateId();
        $activity->generateVerificationCode();

        return $activity;
    }

    public static function fromDatabase(string $id, string $title, string $description, string $country, string $city, string $address, int $totalHours,string $verificationCode): self
    {
        $activity = new Activity($title, $description, $country, $city, $address, $totalHours);

        $activity->id = $id;
        $activity->verificationCode = $verificationCode;

        return $activity;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getCountry(): string
    {
        return $this->country;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getTotalHours(): int {
        return $this->totalHours;
    }

    public function getVerificationCode(): string
    {
        return $this->verificationCode;
    }

    private function generateId(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function regenerateVerificationCode(): void
    {
        $this->generateVerificationCode();
    }

    private function generateVerificationCode(): void
    {
        $this->verificationCode = Uuid::uuid4()->toString();
    }
}
