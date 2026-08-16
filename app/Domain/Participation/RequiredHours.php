<?php

namespace App\Domain\Participation;

readonly class RequiredHours
{
    /**
     * @throws NonPositiveHoursException
     */
    public function __construct(private int $totalHours)
    {
        if ($totalHours <= 0) {
            throw new NonPositiveHoursException();
        }
    }

    public function isSatisfiedBy(float $hours): bool
    {
        return $hours >= $this->totalHours;
    }

    public function getTotalHours(): int
    {
        return $this->totalHours;
    }
}
