<?php

namespace App\Domain\Participation;

interface ParticipationRepository
{
    public function findByActivityIdAndAssistantId(string $activityId, string $assistantId): ?Participation;
    public function create(Participation $participation): void;
    public function update(Participation $participation): void;
}
