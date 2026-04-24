<?php

namespace Tests\Unit\Application\Participation;

use App\Application\Participation\CreateParticipation;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationExistsException;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\Participation\ParticipationStatus;
use App\Domain\Participation\ParticipationVerificationCodeMismatchException;
use PHPUnit\Framework\TestCase;
use Tests\ObjectMother\ActivityMother;
use Tests\ObjectMother\AssistantMother;

class CreateParticipationTest extends TestCase
{
    public function test_create_participation_when_there_is_not_previous_participation()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();

        $participationRepository = $this->createMock(ParticipationRepository::class);

        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn(null);
        $participationRepository->expects($this->once())->method('save');

        $createParticipation = new CreateParticipation($participationRepository);
        $participation = $createParticipation->execute($assistant, $activity, $startTime, $verificationCode);

        $this->assertSame(ParticipationStatus::IN_PROCESS, $participation->status());
    }

    public function test_create_participation_when_there_is_previous_participation_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $participation = new Participation($assistant, $activity, new \DateTimeImmutable());
        $startTime = new \DateTimeImmutable();
        $participationRepository = $this->createStub(ParticipationRepository::class);

        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn($participation);

        $createParticipation = new CreateParticipation($participationRepository);

        $this->expectException(ParticipationExistsException::class);
        $this->expectExceptionMessage('Assistant already participated in this activity.');

        $createParticipation->execute($assistant, $activity, $startTime, $verificationCode);
    }

    public function test_create_participation_with_an_invalid_verification_code_throws_an_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $verificationCode = $activity->getVerificationCode();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $createParticipation = new CreateParticipation($participationRepository);

        $activity->regenerateVerificationCode();

        $this->expectException(ParticipationVerificationCodeMismatchException::class);
        $this->expectExceptionMessage('Invalid verification code provided.');

        $createParticipation->execute($assistant, $activity, $startTime, $verificationCode);
    }
}
