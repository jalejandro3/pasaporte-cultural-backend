<?php

namespace App\Domain\Participation;

interface ParticipationRepository
{
    public function findByActivityIdAndAssistantId(string $activityId, string $assistantId): ?Participation;
    public function save(Participation $participation): void;
}
