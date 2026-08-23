<?php

namespace Tests\Unit\Http\Exceptions;

use App\Application\Activity\NotFoundActivityException;
use App\Application\Participation\ParticipationExistsException;
use App\Application\Participation\ParticipationVerificationCodeMismatchException;
use App\Application\User\NotFoundUserException;
use App\Http\Exceptions\ExceptionRenderer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ExceptionRendererTest extends TestCase
{
    public function test_exception_renderer_not_found_activity_exception()
    {
        $exceptionRenderer = new ExceptionRenderer();
        $notFoundActivityException = new NotFoundActivityException('Activity not found.');
        $response = $exceptionRenderer->render($notFoundActivityException);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            "type" => "https://pasaporte-cultural/errors/activity-not-found",
            "title" => "Activity not found",
            "status" => 404,
            "detail" => "Activity not found."
        ], $response->getData(true));
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_exception_renderer_not_found_user_exception()
    {
        $exceptionRenderer = new ExceptionRenderer();
        $notFoundUserException = new NotFoundUserException('User not found.');
        $response = $exceptionRenderer->render($notFoundUserException);

        $this->assertEquals(Response::HTTP_NOT_FOUND, $response->getStatusCode());
        $this->assertEquals([
            "type" => "https://pasaporte-cultural/errors/user-not-found",
            "title" => "User not found",
            "status" => 404,
            "detail" => "User not found."
        ], $response->getData(true));
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_exception_renderer_participation_exists_exception()
    {
        $exceptionRenderer = new ExceptionRenderer();
        $participationExistsException = new ParticipationExistsException('Assistant already participated in this activity.');
        $response = $exceptionRenderer->render($participationExistsException);

        $this->assertEquals(Response::HTTP_CONFLICT, $response->getStatusCode());
        $this->assertEquals([
            "type" => "https://pasaporte-cultural/errors/participation-already-exists",
            "title" => "Participation already exists",
            "status" => 409,
            "detail" => "Assistant already participated in this activity."
        ], $response->getData(true));
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_exception_renderer_participation_verification_code_mismatch_exception()
    {
        $exceptionRenderer = new ExceptionRenderer();
        $participationVerificationMismatchException = new ParticipationVerificationCodeMismatchException('Invalid verification code provided.');
        $response = $exceptionRenderer->render($participationVerificationMismatchException);

        $this->assertEquals(Response::HTTP_UNPROCESSABLE_ENTITY, $response->getStatusCode());
        $this->assertEquals([
            "type" => "https://pasaporte-cultural/errors/verification-code-mismatch",
            "title" => "Verification code mismatch",
            "status" => 422,
            "detail" => "Invalid verification code provided."
        ], $response->getData(true));
        $this->assertEquals('application/problem+json', $response->headers->get('Content-Type'));
    }

    public function test_exception_renderer_not_mapped_exception()
    {
        $exceptionRenderer = new ExceptionRenderer();
        $response = $exceptionRenderer->render(new \RuntimeException('unmapped'));
        $this->assertNull($response);
    }
}


