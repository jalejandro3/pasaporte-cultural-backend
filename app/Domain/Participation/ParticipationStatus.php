<?php

namespace App\Domain\Participation;

enum ParticipationStatus: string
{
    case IN_PROCESS = 'in_process';
    case COMPLETED = 'completed';
    case NOT_COMPLETED = 'not_completed';
}
