<?php

namespace Tests\Feature;

use App\Domain\Participation\Participation;
use App\Infrastructure\Participation\EloquentParticipationRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ObjectMother\ActivityMother;
use Tests\ObjectMother\AssistantMother;
use Tests\TestCase;

class EloquentParticipationRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_save_participation()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), $startTime);
        $participationRepository = new EloquentParticipationRepository();

        $participationRepository->save($participation);

        $this->assertDatabaseHas('participations', [
            'assistant_id' => $assistant->getId(),
            'activity_id' => $activity->getId(),
            'start_time' => $startTime->format('Y-m-d H:i:s'),
        ]);
    }
}
