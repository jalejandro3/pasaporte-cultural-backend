<?php

namespace Tests\Feature\Infrastructure\Activity;

use App\Infrastructure\Activity\EloquentActivity;
use App\Infrastructure\Activity\EloquentActivityRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EloquentActivityRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_find_by_id()
    {
        $activity = EloquentActivity::create([
            'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'title' => 'Cinema Forum',
            'description' => 'A film discussion session',
            'country' => 'Colombia',
            'city' => 'Barranquilla',
            'address' => 'Calle 1 #2-3',
            'total_hours' => 4,
            'verification_code' => 'f0e1d2c3-b4a5-6789-0abc-def123456789'
        ]);
        $activityRepository = new EloquentActivityRepository();
        $foundActivity = $activityRepository->findById($activity->id);

        $this->assertEquals($activity->id, $foundActivity->getId());
        $this->assertEquals($activity->title, $foundActivity->getTitle());
        $this->assertEquals($activity->description, $foundActivity->getDescription());
        $this->assertEquals($activity->country, $foundActivity->getCountry());
        $this->assertEquals($activity->city, $foundActivity->getCity());
        $this->assertEquals($activity->address, $foundActivity->getAddress());
        $this->assertEquals($activity->total_hours, $foundActivity->getTotalHours());
        $this->assertEquals($activity->verification_code, $foundActivity->getVerificationCode());
    }
}
