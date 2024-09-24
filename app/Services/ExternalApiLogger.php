<?php
namespace App\Services;

use App\Models\ExternalApiLog;

class ExternalApiLogger
{
    public static function log($cron_id,$apiName, $description =null, $endpoint, $method, $requestData, $responseData, $statusCode)
    {
        ExternalApiLog::create([
            'cron_status_id' => $cron_id,
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
