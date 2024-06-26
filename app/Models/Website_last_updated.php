<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Website_last_updated extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'website_id',
        'last_updated_at',
    ];

    public function getLastUpdatedAtAttribute($value)
    {
        return Carbon::parse($value)->format('d M g:i A');
    }
}