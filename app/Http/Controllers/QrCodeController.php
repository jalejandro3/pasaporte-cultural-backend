<?php

namespace App\Http\Controllers;

use App\Exceptions\InputValidationException;
use App\Services\QrCodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class QrCodeController extends Controller
{
    public function __construct(private readonly QrCodeService $qrCodeService)
    {
    }

    /**
     * @throws InputValidationException
     */
    public function regenerateCode(Request $request): JsonResponse
    {
        $rules = [
            'activity_id' => 'required|exists:activities,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            throw new InputValidationException($validator->getMessageBag()->toJson());
        }

        return $this->success($this->qrCodeService->regenerateCode($request->activity_id));
    }
}
