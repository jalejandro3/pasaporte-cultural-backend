<?php

namespace Tests\Unit\Domain;

use App\Domain\Activity\Activity;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    public function test_activity_creation_with_unique_verification_code()
    {
        $id = 1;
        $title = 'Activity Title';
        $description = 'Activity Description';
        $country = 'United States';
        $city = 'Buffalo';
        $address = '110 Fairview Road';
        $totalHours = 2;
        $activity = new Activity($id, $title, $description, $country, $city, $address, $totalHours);

        $firstVerificationCode = $activity->getVerificationCode();
        $secondVerificationCode = $activity->getVerificationCode();

        $this->assertNotEmpty($firstVerificationCode);
        $this->assertEquals($firstVerificationCode, $secondVerificationCode);
    }

    public function test_activity_re_generate_verification_code_get_new_verification_code()
    {
        $id = 1;
        $title = 'Activity Title';
        $description = 'Activity Description';
        $country = 'United States';
        $city = 'Buffalo';
        $address = '110 Fairview Road';
        $totalHours = 2;
        $activity = new Activity($id, $title, $description, $country, $city, $address, $totalHours);

        $oldVerificationCode = $activity->getVerificationCode();

        $activity->regenerateVerificationCode();

        $newVerificationCode = $activity->getVerificationCode();

        $this->assertNotEquals($oldVerificationCode, $newVerificationCode);
    }
}
