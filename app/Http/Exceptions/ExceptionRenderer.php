<?php

namespace App\Http\Exceptions;

use App\Application\Activity\NotFoundActivityException;
use App\Application\Participation\ParticipationExistsException;
use App\Application\Participation\ParticipationVerificationCodeMismatchException;
use App\Application\User\NotFoundUserException;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class ExceptionRenderer
{
    private array $problemDetails;

    public function __construct()
    {
        $this->problemDetails = [
            NotFoundActivityException::class => new ProblemDetail(
                'https://pasaporte-cultural/errors/activity-not-found',
                'Activity not found',
                Response::HTTP_NOT_FOUND
            ),
            NotFoundUserException::class => new ProblemDetail(
                'https://pasaporte-cultural/errors/user-not-found',
                'User not found',
                Response::HTTP_NOT_FOUND
            ),
            ParticipationExistsException::class => new ProblemDetail(
                'https://pasaporte-cultural/errors/participation-already-exists',
                'Participation already exists',
                Response::HTTP_CONFLICT
            ),
            ParticipationVerificationCodeMismatchException::class => new ProblemDetail(
                'https://pasaporte-cultural/errors/verification-code-mismatch',
                'Verification code mismatch',
                Response::HTTP_UNPROCESSABLE_ENTITY
            )
        ];
    }

    public function render(Throwable $e): ?JsonResponse
    {
        $exceptionClass = get_class($e);

        if (!isset($this->problemDetails[$exceptionClass])) {
            return null;
        }

        $problemDetail = $this->problemDetails[$exceptionClass];
        $response = new JsonResponse(
            $problemDetail->toResponseBody($e->getMessage()),
            $problemDetail->status
        );

        $response->headers->set('Content-Type', 'application/problem+json');

        return $response;
    }
}
