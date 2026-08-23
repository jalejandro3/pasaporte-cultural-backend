<?php

namespace Tests\Unit\Application\Participation;

use App\Application\Activity\NotFoundActivityException;
use App\Application\Participation\CreateParticipation;
use App\Application\Participation\ParticipationExistsException;
use App\Application\Participation\ParticipationVerificationCodeMismatchException;
use App\Application\User\NotFoundUserException;
use App\Domain\Activity\ActivityRepository;
use App\Domain\Participation\Participation;
use App\Domain\Participation\ParticipationRepository;
use App\Domain\Participation\ParticipationStatus;
use App\Domain\User\UserRepository;
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
        $activityRepository = $this->createStub(ActivityRepository::class);
        $userRepository = $this->createStub(UserRepository::class);

        $userRepository->method('findById')->willReturn($assistant);
        $activityRepository->method('findById')->willReturn($activity);
        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn(null);
        $participationRepository->expects($this->once())->method('create');

        $createParticipation = new CreateParticipation($participationRepository, $activityRepository, $userRepository);
        $participation = $createParticipation->execute($assistant->getId(), $activity->getId(), $startTime, $verificationCode);

        $this->assertSame(ParticipationStatus::IN_PROCESS, $participation->status());
    }

    public function test_create_participation_when_there_is_previous_participation_throws_exception()
    {
        $activity = ActivityMother::create(2);
        $verificationCode = $activity->getVerificationCode();
        $assistant = AssistantMother::create();
        $participation = Participation::create($assistant->getId(), $activity->getId(), $activity->getTotalHours(), new \DateTimeImmutable());
        $startTime = new \DateTimeImmutable();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $activityRepository = $this->createStub(ActivityRepository::class);
        $userRepository = $this->createStub(UserRepository::class);

        $userRepository->method('findById')->willReturn($assistant);
        $activityRepository->method('findById')->willReturn($activity);
        $participationRepository->method('findByActivityIdAndAssistantId')->willReturn($participation);

        $createParticipation = new CreateParticipation($participationRepository, $activityRepository, $userRepository);

        $this->expectException(ParticipationExistsException::class);
        $this->expectExceptionMessage('Assistant already participated in this activity.');

        $createParticipation->execute($assistant->getId(), $activity->getId(), $startTime, $verificationCode);
    }

    public function test_create_participation_with_an_invalid_verification_code_throws_an_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $verificationCode = $activity->getVerificationCode();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $activityRepository = $this->createStub(ActivityRepository::class);
        $userRepository = $this->createStub(UserRepository::class);

        $activityRepository->method('findById')->willReturn($activity);

        $createParticipation = new CreateParticipation($participationRepository, $activityRepository, $userRepository);

        $activity->regenerateVerificationCode();

        $this->expectException(ParticipationVerificationCodeMismatchException::class);
        $this->expectExceptionMessage('Invalid verification code provided.');

        $createParticipation->execute($assistant->getId(), $activity->getId(), $startTime, $verificationCode);
    }

    public function test_create_participation_with_an_assistant_id_throws_not_found_an_user_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $verificationCode = $activity->getVerificationCode();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $activityRepository = $this->createStub(ActivityRepository::class);
        $userRepository = $this->createStub(UserRepository::class);

        $userRepository->method('findById')->willReturn(null);
        $activityRepository->method('findById')->willReturn($activity);

        $createParticipation = new CreateParticipation($participationRepository, $activityRepository, $userRepository);

        $this->expectException(NotFoundUserException::class);
        $this->expectExceptionMessage('Assistant not found.');
        $createParticipation->execute($assistant->getId(), $activity->getId(), $startTime, $verificationCode);
    }

    public function test_create_participation_with_an_activity_id_throws_a_not_found_activity_exception()
    {
        $activity = ActivityMother::create(2);
        $assistant = AssistantMother::create();
        $startTime = new \DateTimeImmutable();
        $verificationCode = $activity->getVerificationCode();
        $participationRepository = $this->createStub(ParticipationRepository::class);
        $activityRepository = $this->createStub(ActivityRepository::class);
        $userRepository = $this->createStub(UserRepository::class);

        $activityRepository->method('findById')->willReturn(null);

        $createParticipation = new CreateParticipation($participationRepository, $activityRepository, $userRepository);

        $this->expectException(NotFoundActivityException::class);
        $this->expectExceptionMessage('Activity not found.');
        $createParticipation->execute($assistant->getId(), $activity->getId(), $startTime, $verificationCode);
    }
}
