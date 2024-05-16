<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'client_id',
        'client_secret_id',
        'redirect_url',
        'refresh_token',
        'access_token',
        'expiry_time',
        'status',
    ];
}
