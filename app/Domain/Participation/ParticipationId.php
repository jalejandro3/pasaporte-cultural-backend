<?php

namespace App\Domain\Participation;

use Ramsey\Uuid\Uuid;

class ParticipationId
{
    private string $id;
    private function __construct() {}

    public static function fromString(string $id): self
    {
        if (!Uuid::isValid($id)) {
            throw new InvalidUuidException();
        }

        $participationId = new ParticipationId();

        $participationId->id = $id;

        return $participationId;
    }

    public static function generate(): self
    {
        $participationId = new ParticipationId();

        $participationId->generateId();

        return $participationId;
    }

    private function generateId(): void
    {
        $this->id = Uuid::uuid4()->toString();
    }

    public function equals(ParticipationId $other): bool
    {
        return $this->id === $other->id;
    }

    public function value(): string
    {
        return $this->id;
    }
}
