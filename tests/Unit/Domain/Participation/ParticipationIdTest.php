<?php

namespace Tests\Unit\Domain\Participation;

use App\Domain\Participation\InvalidUuidException;
use App\Domain\Participation\ParticipationId;
use PHPUnit\Framework\TestCase;

class ParticipationIdTest extends TestCase
{
    public function test_participation_id_from_string_with_wrong_format_throws_exception()
    {
        $this->expectException(InvalidUuidException::class);
        ParticipationId::fromString('wrong-format');
    }

    public function test_participation_id_from_string_with_valid_format_returns_participation_id()
    {
        $uuid = '123e4567-e89b-12d3-a456-426655440000';
        $participationId = ParticipationId::fromString($uuid);

        $this->assertEquals($uuid, $participationId->value());
    }

    public function test_participation_id_two_participation_ids_are_equal_if_they_have_the_same_value()
    {
        $uuid = '123e4567-e89b-12d3-a456-426655440000';
        $participationId1 = ParticipationId::fromString($uuid);
        $participationId2 = ParticipationId::fromString($uuid);

        $this->assertTrue($participationId1->equals($participationId2));
    }

    public function test_participation_id_two_participation_ids_are_not_equal_if_they_have_different_values()
    {
        $participationId1 = ParticipationId::fromString('123e4567-e89b-12d3-a456-426655440000');
        $participationId2 = ParticipationId::fromString('54321fed-cba9-8765-4321-fedcba987654');
        $this->assertFalse($participationId1->equals($participationId2));
    }
}
