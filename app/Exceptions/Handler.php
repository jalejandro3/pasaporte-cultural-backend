<?php

namespace App\Exceptions;

use Carbon\Carbon;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    private array $errors = [
        [
            'class' => ApplicationException::class,
            'error_message' => 'Bad Request.',
            'response_code' => Response::HTTP_BAD_REQUEST
        ],
        [
            'class' => UnauthorizedException::class,
            'error_message' => 'Access Denied.',
            'response_code' => Response::HTTP_UNAUTHORIZED
        ],
        [
            'class' => ResourceNotFoundException::class,
            'error_message' => 'Resource not found.',
            'response_code' => Response::HTTP_NOT_FOUND
        ]
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e): JsonResponse
    {
        if ($error = $this->exceptionExists($e)) {
            return $this->getResponse($e, $error);
        }

        return $this->getResponse($e);
    }

    private function exceptionExists(Throwable $exception): bool|array
    {
        foreach ($this->errors as $error) {
            if ($exception instanceof $error['class']) {
                return $error;
            }
        }

        return false;
    }

    private function getResponse(Throwable $e, array $error = null): JsonResponse
    {
        if (is_null($error)) {
            return $this->getErrorResponse(
                'An error occurred!',
                $e,
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

        $errorMessage = $this->getMessageError($e, $error);
        $responseCode = $this->getResponseCode($e, $error);

        return $this->getErrorResponse($errorMessage, $e, $responseCode);
    }

    private function getErrorResponse(?string $message, Throwable $e, int $code): JsonResponse
    {
        $errorMsg = $this->extractErrorMessage($e->getMessage());

        return response()->json([
            'timestamp' => Carbon::now(),
            'error' => $errorMsg ?? $e->getMessage(),
            'message' => $message
        ], $code);
    }

    private function getMessageError(Throwable $e, array $error): string
    {
        return ($e->getMessage() === ''
            && isset($error['error_message'])) ? $error['error_message'] : $e->getMessage();
    }

    private function getResponseCode(Throwable $e, array $error): int
    {
        return ($e->getCode() === 0 &&
            isset($error['response_code'])) ? $error['response_code'] : $e->getCode();
    }

    private function extractErrorMessage(string $exceptionMessage): ?string
    {
        $msg = null;
        $decodedMessage = json_decode($exceptionMessage, true);

        if (!is_array($decodedMessage)) {
            return null;
        }

        foreach ($decodedMessage as $messages) {
            if (is_array($messages) && isset($messages[0])) {
                $msg = $messages[0];
            }
        }

        return $msg;
    }
}
