<?php

namespace Tests\Unit\Application\Participation;

use App\Application\Participation\FinishParticipation;
use App\Domain\Participation\NotFoundParticipationException;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\Participation\ParticipationStatus;
use App\Domain\Participation\ParticipationVerificationCodeMismatchException;
use PHPUnit\Framework\TestCase;
use Tests\ObjectMother\ActivityMother;
use Tests\ObjectMother\AssistantMother;

class FinishParticipationTest extends TestCase
{
    public function test_finish_participation_with_completed_status_when_there_is_an_in_process_participation()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $participation = new Participation($assistant, $activity, new \DateTimeImmutable());
        $participationRepository = $this->createMock(ParticipationRepository::class);

        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn($participation);
        $participationRepository->expects($this->once())->method('save');

        $finishParticipation = new FinishParticipation($participationRepository);

        $finishParticipation->execute($assistant, $activity, new \DateTimeImmutable('+2 hours'), $verificationCode);

        $this->assertSame(ParticipationStatus::COMPLETED, $participation->status());
    }

    public function test_finish_participation_with_not_completed_status_when_there_is_an_in_process_participation()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $participation = new Participation($assistant, $activity, new \DateTimeImmutable());
        $participationRepository = $this->createMock(ParticipationRepository::class);

        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn($participation);
        $participationRepository->expects($this->once())->method('save');

        $finishParticipation = new FinishParticipation($participationRepository);

        $finishParticipation->execute($assistant, $activity, new \DateTimeImmutable('+1 hours'), $verificationCode);

        $this->assertSame(ParticipationStatus::NOT_COMPLETED, $participation->status());
    }

    public function test_finish_participation_not_previously_started_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $participationRepository = $this->createStub(ParticipationRepository::class);

        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn(null);

        $finishParticipation = new FinishParticipation($participationRepository);

        $this->expectException(NotFoundParticipationException::class);
        $this->expectExceptionMessage('Participation not found.');

        $finishParticipation->execute($assistant, $activity, new \DateTimeImmutable('+2 hours'), $verificationCode);
    }

    public function test_finish_participation_with_an_invalid_verification_code_throws_an_exception()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $finishParticipation = new FinishParticipation($participationRepository);

        $activity->regenerateVerificationCode();

        $this->expectException(ParticipationVerificationCodeMismatchException::class);
        $this->expectExceptionMessage('Invalid verification code provided.');

        $finishParticipation->execute($assistant, $activity, $startTime, $verificationCode);
    }
}
