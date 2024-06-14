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
        'last_updated_at',
    ];

    public function backlinks()
    {
        return $this->hasMany(Backlink::class);
    }
}
