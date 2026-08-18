<?php

namespace App\Http\Controllers;

use App\Application\Participation\CreateParticipation;
use App\Http\Requests\CreateParticipationRequest;
use App\Http\Resources\ParticipationResource;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ParticipationController extends Controller
{
    public function __construct(
        private readonly CreateParticipation $createParticipation,
    ) {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateParticipationRequest $request): JsonResponse
    {
        $data = $request->validated();
        $startTime = new \DateTimeImmutable();
        $newParticipation = $this->createParticipation->execute($data['assistant_id'], $data['activity_id'], $startTime, $data['verification_code']);

        return (new ParticipationResource($newParticipation))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
