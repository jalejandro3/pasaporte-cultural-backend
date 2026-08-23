<?php

namespace Tests\Feature\Http;

use App\Domain\Participation\ParticipationStatus;
use App\Domain\User\UserRole;
use App\Infrastructure\Activity\EloquentActivity;
use App\Infrastructure\Participation\EloquentParticipation;
use App\Infrastructure\User\EloquentUser;
use App\Infrastructure\User\EloquentUserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\HttpFoundation\Response;
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

        $response->assertStatus(Response::HTTP_CREATED);
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

    public function test_create_participation_throws_not_found_activity_exception()
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

        $response = $this->postJson('/api/participations', [
            'activity_id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567880',
            'assistant_id' => '123e4567-e89b-12d3-a456-426655440000',
            'verification_code' => $activity->verification_code,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            'type' => 'https://pasaporte-cultural/errors/activity-not-found',
            'title' => 'Activity not found',
            'status' => 404,
            'detail' => 'Activity not found.',
        ]);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_create_participation_throws_participation_verification_code_mismatch_exception()
    {
        $activity = EloquentActivity::create([
            'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'title' => 'Cinema Forum',
            'description' => 'A film discussion session',
            'country' => 'Colombia',
            'city' => 'Barranquilla',
            'address' => 'Calle 1 #2-3',
            'total_hours' => 4,
            'verification_code' => 'f0e1d2c3-b4a5-6789-0abc-def123456789',
        ]);

        $response = $this->postJson('/api/participations', [
            'activity_id' => $activity->id,
            'assistant_id' => '123e4567-e89b-12d3-a456-426655440000',
            'verification_code' => 'wrong-verification-code',
        ]);

        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $response->assertJson([
            "type" => "https://pasaporte-cultural/errors/verification-code-mismatch",
            "title" => "Verification code mismatch",
            "status" => 422,
            "detail" => "Invalid verification code provided.",
        ]);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_create_participation_throws_not_found_user_exception()
    {
        $activity = EloquentActivity::create([
            'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'title' => 'Cinema Forum',
            'description' => 'A film discussion session',
            'country' => 'Colombia',
            'city' => 'Barranquilla',
            'address' => 'Calle 1 #2-3',
            'total_hours' => 4,
            'verification_code' => 'f0e1d2c3-b4a5-6789-0abc-def123456789',
        ]);

        $response = $this->postJson('/api/participations', [
            'activity_id' => $activity->id,
            'assistant_id' => '123e4567-e89b-12d3-a456-426655440000',
            'verification_code' => $activity->verification_code,
        ]);

        $response->assertStatus(Response::HTTP_NOT_FOUND);
        $response->assertJson([
            "type" => "https://pasaporte-cultural/errors/user-not-found",
            "title" => "User not found",
            "status" => 404,
            "detail" => "Assistant not found.",
        ]);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_create_participation_throws_participation_exists_exception()
    {
        $activity = EloquentActivity::create([
            'id' => 'a1b2c3d4-e5f6-7890-abcd-ef1234567890',
            'title' => 'Cinema Forum',
            'description' => 'A film discussion session',
            'country' => 'Colombia',
            'city' => 'Barranquilla',
            'address' => 'Calle 1 #2-3',
            'total_hours' => 4,
            'verification_code' => 'f0e1d2c3-b4a5-6789-0abc-def123456789',
        ]);

        $assistant = EloquentUser::create([
            'id' => '123e4567-e89b-12d3-a456-426655440000',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'id_document' => '123456789',
            'email' => 'assistant@unir.net',
            'role' => UserRole::ASSISTANT->value,
            'password' => bcrypt('password'),
        ]);

        EloquentParticipation::create([
            'id' => '123e4567-e89b-12d3-a456-426655440001',
            'activity_id' => $activity->id,
            'assistant_id' => $assistant->id,
            'required_hours' => $activity->total_hours,
            'status' => ParticipationStatus::IN_PROCESS->value,
            'start_time' => new \DateTimeImmutable(),
        ]);

        $response = $this->postJson('/api/participations', [
            'activity_id' => $activity->id,
            'assistant_id' => $assistant->id,
            'verification_code' => $activity->verification_code,
        ]);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $response->assertJson([
            "type" => "https://pasaporte-cultural/errors/participation-already-exists",
            "title" => "Participation already exists",
            "status" => 409,
            "detail" => "Assistant already participated in this activity."
        ]);
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }
}
