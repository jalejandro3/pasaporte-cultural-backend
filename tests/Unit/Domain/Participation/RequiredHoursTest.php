<?php

namespace Tests\Unit\Domain\Participation;

use App\Domain\Participation\NonPositiveHoursException;
use App\Domain\Participation\RequiredHours;
use PHPUnit\Framework\TestCase;

class RequiredHoursTest extends TestCase
{
    public function test_required_hours_above_the_total_hours_returns_true()
    {
        $totalHours = 2;
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+3 hour');
        $participationHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
        $requiredHours = new RequiredHours($totalHours);

        $this->assertTrue($requiredHours->isSatisfiedBy($participationHours));
    }

    public function test_required_hours_below_the_total_hours_returns_false()
    {
        $totalHours = 2;
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+1 hour');
        $participationHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
        $requiredHours = new RequiredHours($totalHours);

        $this->assertFalse($requiredHours->isSatisfiedBy($participationHours));
    }

    public function test_required_hours_equals_the_total_hours_returns_true()
    {
        $totalHours = 2;
        $startTime = new \DateTimeImmutable();
        $endTime = new \DateTimeImmutable('+2 hour');
        $participationHours = ($endTime->getTimestamp() - $startTime->getTimestamp()) / 3600;
        $requiredHours = new RequiredHours($totalHours);

        $this->assertTrue($requiredHours->isSatisfiedBy($participationHours));
    }

    public function test_required_hours_with_zero_hours_throws_exception()
    {
        $this->expectException(NonPositiveHoursException::class);
        new RequiredHours(0);
    }

    public function test_required_hours_with_negative_hours_throws_exception()
    {
        $this->expectException(NonPositiveHoursException::class);
        new RequiredHours(-1);
    }
}
