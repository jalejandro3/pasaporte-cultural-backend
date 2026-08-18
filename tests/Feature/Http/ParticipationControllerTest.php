<?php

namespace Tests\Feature\Http;

use App\Domain\Participation\ParticipationStatus;
use App\Infrastructure\Activity\EloquentActivity;
use App\Infrastructure\User\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\ObjectMother\AssistantMother;
use Tests\TestCase;

class ParticipationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_participation()
    {
        $assistant = AssistantMother::create();
        $password = 'password';
        $userRepository = new EloquentUserRepository();

        $userRepository->save($assistant, $password);

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

        $response = $this->postJson('/api/participations', [
            'activity_id' => $activity->id,
            'assistant_id' => $assistant->getId(),
            'verification_code' => $activity->verification_code,
        ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'data' => [
                'id',
                'activity_id',
                'assistant_id',
                'status'
            ]
        ]);

        $this->assertDatabaseHas('participations', [
            'assistant_id' => $assistant->getId(),
            'activity_id' => $activity->id,
            'status' => ParticipationStatus::IN_PROCESS->value,
            'required_hours' => $activity->total_hours,
        ]);
    }
}
