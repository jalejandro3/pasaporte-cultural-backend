<?php

namespace Tests\Unit\Domain;

use App\Domain\Participation\FinishedParticipationException;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationStatus;
use App\Domain\Participation\PriorEndDateParticipationException;
use PHPUnit\Framework\TestCase;
use Tests\ObjectMother\ActivityMother;
use Tests\ObjectMother\AssistantMother;

class ParticipationTest extends TestCase
{
    public function test_participation_initiated_returns_in_process_status()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $participation = new Participation($assistant, $activity, $startTime);

        $this->assertSame(ParticipationStatus::IN_PROCESS, $participation->status());
    }

    public function test_participation_completed_with_full_hours_returns_completed_status()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+2 hour');
        $participation = new Participation($assistant, $activity, $startTime);

        $participation->finish($endTime);

        $this->assertSame(ParticipationStatus::COMPLETED, $participation->status());
    }

    public function test_participation_completed_with_less_hours_returns_not_completed_status()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+1 hour');
        $participation = new Participation($assistant, $activity, $startTime);

        $participation->finish($endTime);

        $this->assertSame(ParticipationStatus::NOT_COMPLETED, $participation->status());
    }

    public function test_participation_with_completed_status_completed_again_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+2 hour');
        $participation = new Participation($assistant, $activity, $startTime);

        $participation->finish($endTime);

        $this->ExpectException(FinishedParticipationException::class);
        $this->ExpectExceptionMessage('Participation is already finished.');

        $participation->finish($endTime);
    }

    public function test_participation_with_not_completed_status_completed_again_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+1 hour');
        $participation = new Participation($assistant, $activity, $startTime);

        $participation->finish($endTime);

        $this->ExpectException(FinishedParticipationException::class);
        $this->ExpectExceptionMessage('Participation is already finished.');

        $participation->finish($endTime);
    }

    public function test_participation_with_end_date_prior_start_date_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('-1 hour');
        $participation = new Participation($assistant, $activity, $startTime);

        $this->ExpectException(PriorEndDateParticipationException::class);
        $this->ExpectExceptionMessage('The end time cannot be before the start time.');

        $participation->finish($endTime);
    }
}
