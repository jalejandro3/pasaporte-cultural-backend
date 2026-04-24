<?php

namespace App\Domain\User;

enum UserRole: string
{
    case ASSISTANT = 'assistant';
    case ADMIN = 'admin';
}
