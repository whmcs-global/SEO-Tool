<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Website extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'url',
        'GOOGLE_ANALYTICS_CLIENT_ID',
        'GOOGLE_ANALYTICS_CLIENT_SECRET',
        'GOOGLE_ANALYTICS_REDIRECT_URI',
        'API_KEY',
        'GOOGLE_ADS_DEVELOPER_TOKEN',
        'GOOGLE_ADS_CLIENT_ID',
        'GOOGLE_ADS_CLIENT_SECRET',
        'GOOGLE_ADS_REDIRECT_URI',
        'GOOGLE_ADS_KEY',
        'GOOGLE_ADS_LOGIN_CUSTOMER_ID',
    ];

    public function backlinks()
    {
        return $this->hasMany(Backlink::class);
    }
}
