<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\QrCode;

interface QrCodeService
{
    public function generateCode(Activity $activity): QrCode;
}
