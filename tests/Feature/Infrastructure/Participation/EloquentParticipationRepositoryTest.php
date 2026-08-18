<?php

namespace Tests\Feature\Infrastructure\Participation;

use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationStatus;
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

    public function test_find_by_activity_id_and_assistant_id()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), $startTime);
        $participationRepository = new EloquentParticipationRepository();
        $participationRepository->save($participation);

        $foundParticipation = $participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        $this->assertEquals($participation->getId()->value(), $foundParticipation->getId()->value());
        $this->assertEquals($participation->getAssistantId(), $foundParticipation->getAssistantId());
        $this->assertEquals($participation->getActivityId(), $foundParticipation->getActivityId());
        $this->assertEquals($participation->getStartTime()->getTimestamp(), $foundParticipation->getStartTime()->getTimestamp());
        $this->assertEquals($participation->getEndTime()?->getTimestamp(), $foundParticipation->getEndTime()?->getTimestamp());
    }

    public function test_find_by_activity_id_and_assistant_id_returns_null_if_not_found()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $participationRepository = new EloquentParticipationRepository();
        $foundParticipation = $participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        $this->assertNull($foundParticipation);
    }

    public function test_find_by_activity_id_and_assistant_id_found_finished_participation()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+1 hour');
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), $startTime);
        $participationRepository = new EloquentParticipationRepository();

        $participation->finish($endTime);
        $participationRepository->save($participation);

        $foundParticipation = $participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());
        $this->assertEquals($participation->getId()->value(), $foundParticipation->getId()->value());
        $this->assertEquals($participation->getAssistantId(), $foundParticipation->getAssistantId());
        $this->assertEquals($participation->getActivityId(), $foundParticipation->getActivityId());
        $this->assertEquals($participation->getStartTime()->getTimestamp(), $foundParticipation->getStartTime()->getTimestamp());
        $this->assertEquals($participation->getEndTime()->getTimestamp(), $foundParticipation->getEndTime()->getTimestamp());
    }

    public function test_find_by_activity_id_and_assistant_id_found_finished_completed_participation()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+3 hour');
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), $startTime);
        $participation->finish($endTime);
        $participationRepository = new EloquentParticipationRepository();

        $participationRepository->save($participation);

        $foundParticipation = $participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        $this->assertEquals(ParticipationStatus::COMPLETED, $foundParticipation->status());

        $this->assertEquals($participation->getId()->value(), $foundParticipation->getId()->value());
        $this->assertEquals($participation->getAssistantId(), $foundParticipation->getAssistantId());
        $this->assertEquals($participation->getActivityId(), $foundParticipation->getActivityId());
        $this->assertEquals($participation->getStartTime()->getTimestamp(), $foundParticipation->getStartTime()->getTimestamp());
        $this->assertEquals($participation->getEndTime()->getTimestamp(), $foundParticipation->getEndTime()->getTimestamp());
    }

    public function test_find_by_activity_id_and_assistant_id_found_finished_not_completed_participation()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+1 hour');
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), $startTime);
        $participation->finish($endTime);
        $participationRepository = new EloquentParticipationRepository();

        $participationRepository->save($participation);

        $foundParticipation = $participationRepository->findByActivityIdAndAssistantId($activity->getId(), $assistant->getId());

        $this->assertEquals(ParticipationStatus::NOT_COMPLETED, $foundParticipation->status());

        $this->assertEquals($participation->getId()->value(), $foundParticipation->getId()->value());
        $this->assertEquals($participation->getAssistantId(), $foundParticipation->getAssistantId());
        $this->assertEquals($participation->getActivityId(), $foundParticipation->getActivityId());
        $this->assertEquals($participation->getStartTime()->getTimestamp(), $foundParticipation->getStartTime()->getTimestamp());
        $this->assertEquals($participation->getEndTime()->getTimestamp(), $foundParticipation->getEndTime()->getTimestamp());
    }
}
