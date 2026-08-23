<?php

namespace Tests\Unit\Http\Exceptions;

use App\Http\Exceptions\ProblemDetail;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Response;

class ProblemDetailTest extends TestCase
{
    public function test_problem_detail_activity_not_found_with_detail_creation()
    {
        $type = '/activity-not-found';
        $title = 'Activity not found';
        $status = Response::HTTP_NOT_FOUND;
        $message = 'Activity not found.';

        $problemDetail = new ProblemDetail($type, $title, $status);
        $fullException = $problemDetail->toResponseBody($message);

        $this->assertArrayHasKey('type', $fullException);
        $this->assertArrayHasKey('title', $fullException);
        $this->assertArrayHasKey('status', $fullException);
        $this->assertArrayHasKey('detail', $fullException);

        $this->assertEquals($type, $fullException['type']);
        $this->assertEquals($title, $fullException['title']);
        $this->assertEquals($status, $fullException['status']);
        $this->assertEquals($message, $fullException['detail']);
    }
}
