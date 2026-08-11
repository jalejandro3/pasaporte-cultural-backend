<?php

namespace Tests\Unit\Domain;

use App\Domain\Activity\Activity;
use PHPUnit\Framework\TestCase;

class ActivityTest extends TestCase
{
    public function test_activity_creation_with_unique_verification_code()
    {
        $title = 'Activity Title';
        $description = 'Activity Description';
        $country = 'United States';
        $city = 'Buffalo';
        $address = '110 Fairview Road';
        $totalHours = 2;
        $activity = Activity::create($title, $description, $country, $city, $address, $totalHours);

        $firstVerificationCode = $activity->getVerificationCode();
        $secondVerificationCode = $activity->getVerificationCode();

        $this->assertNotEmpty($firstVerificationCode);
        $this->assertEquals($firstVerificationCode, $secondVerificationCode);
    }

    public function test_activity_re_generate_verification_code_get_new_verification_code()
    {
        $title = 'Activity Title';
        $description = 'Activity Description';
        $country = 'United States';
        $city = 'Buffalo';
        $address = '110 Fairview Road';
        $totalHours = 2;
        $activity = Activity::create($title, $description, $country, $city, $address, $totalHours);

        $oldVerificationCode = $activity->getVerificationCode();

        $activity->regenerateVerificationCode();

        $newVerificationCode = $activity->getVerificationCode();

        $this->assertNotEquals($oldVerificationCode, $newVerificationCode);
    }

    public function test_activity_reconstituted_from_database_keeps_its_id_and_verification_code()
    {
        $id = 'a1b2c3d4-e5f6-7890-abcd-ef1234567890';
        $verificationCode = 'f0e1d2c3-b4a5-6789-0abc-def123456789';

        $activity = Activity::fromDatabase(
            $id,
            'Cinema Forum',
            'A film discussion session',
            'Colombia',
            'Barranquilla',
            'Calle 1 #2-3',
            4,
            $verificationCode,
        );

        $this->assertSame($id, $activity->getId());
        $this->assertSame($verificationCode, $activity->getVerificationCode());
    }
}
