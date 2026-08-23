<?php

namespace App\Http\Exceptions;

readonly class ProblemDetail
{
    public function __construct(public string $type, public string $title, public int $status) {}

    public function toResponseBody(string $detail): array
    {
        return [
            'type' => $this->type,
            'title' => $this->title,
            'status' => $this->status,
            'detail' => $detail
        ];
    }
}
