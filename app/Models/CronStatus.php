<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CronStatus extends Model
{
    use HasFactory;

    protected $table = 'cron_status';

    protected $fillable = [
        'cron_name',
        'date',
        'status',
    ];

    public function externalApiLogs()
    {
        return $this->hasMany(ExternalApiLog::class);
    }
}
