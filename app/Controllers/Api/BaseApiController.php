<?php

namespace App\Controllers\Api;

use CodeIgniter\RESTful\ResourceController;

class BaseApiController extends ResourceController
{
    protected $format = 'json';

    protected function respondWithError(string $message, int $statusCode = 400, mixed $error = null)
    {
        $response = [
            'status' => 'error',
            'message' => $message
        ];

        if ($error !== null) {
            $response['error'] = $error;
        }

        return $this->respond($response, $statusCode);
    }

    protected function respondWithSuccess(mixed $data, string $message = 'Success', int $statusCode = 200)
    {
        return $this->respond([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ], $statusCode);
    }
}
