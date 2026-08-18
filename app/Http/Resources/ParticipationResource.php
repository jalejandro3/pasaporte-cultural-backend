<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParticipationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId()->value(),
            'activity_id' => $this->resource->getActivityId(),
            'assistant_id' => $this->resource->getAssistantId(),
            'status' => $this->resource->status()->value,
        ];
    }
}
