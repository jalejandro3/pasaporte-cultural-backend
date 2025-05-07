<?php

namespace App\Workflows;

use App\Enums\ActivityStatus;
use InvalidArgumentException;

class ActivityWorkflow
{
    private const array TRANSITIONS = [
        ActivityStatus::IN_PROGRESS->value => [
            ActivityStatus::COMPLETED->value,
            ActivityStatus::NOT_COMPLETED->value
        ]
    ];

    public static function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? []);
    }

    public static function ensureTransitionIsValid(string $from, string $to): void
    {
        if (!self::canTransition($from, $to)) {
            throw new InvalidArgumentException("It is not possible to transition from $from to status $to.");
        }
    }
}
