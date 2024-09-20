<?php
namespace App\Services;

use App\Models\ExternalApiLog;

class ExternalApiLogger
{
    public static function log($apiName, $description =null, $endpoint, $method, $requestData, $responseData, $statusCode)
    {
        ExternalApiLog::create([
            'api_name' => $apiName,
            'description' => $description,
            'endpoint' => $endpoint,
            'method' => $method,
            'request_data' => $requestData,
            'response_data' => $responseData,
            'status_code' => $statusCode,
        ]);
    }
}
