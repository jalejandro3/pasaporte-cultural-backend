<?php

namespace Tests\Unit\Application\Activity;

use App\Application\Activity\ShowActivity;
use App\Domain\Activity\ActivityRepository;
use PHPUnit\Framework\TestCase;
use Tests\ObjectMother\ActivityMother;
use Tests\ObjectMother\AdminMother;
use Tests\ObjectMother\AssistantMother;

class ShowActivityTest extends TestCase
{
    public function test_show_activity_to_admin_user_returns_verification_code()
    {
        $activity = ActivityMother::create();
        $admin = AdminMother::create();
        $activityRepository = $this->createStub(ActivityRepository::class);

        $activityRepository->method('findById')->willReturn($activity);

        $showActivity = new ShowActivity($activityRepository);
        $result = $showActivity->execute($admin, $activity->getId());

        $this->assertEquals($activity->getVerificationCode(), $result->verificationCode);
    }

    public function test_show_activity_to_assistant_user_returns_verification_code_null()
    {
        $activity = ActivityMother::create();
        $assistant = AssistantMother::create();
        $activityRepository = $this->createStub(ActivityRepository::class);

        $activityRepository->method('findById')->willReturn($activity);

        $showActivity = new ShowActivity($activityRepository);
        $result = $showActivity->execute($assistant, $activity->getId());

        $this->assertNull($result->verificationCode);
    }
}
