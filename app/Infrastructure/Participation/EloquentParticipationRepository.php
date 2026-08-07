<?php

namespace App\Infrastructure\Participation;

use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;

class EloquentParticipationRepository implements ParticipationRepository
{
    public function findByActivityIdAndAssistantId(int $activityId, string $assistantId): ?Participation
    {
    }

    public function save(Participation $participation): void
    {
    }
}
