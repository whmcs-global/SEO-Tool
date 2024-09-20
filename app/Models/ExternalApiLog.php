<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExternalApiLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_name', 'description', 'endpoint', 'method', 'request_data', 'response_data', 'status_code'
    ];

    protected $casts = [
        'request_data' => 'array',
        'response_data' => 'array',
    ];
}
