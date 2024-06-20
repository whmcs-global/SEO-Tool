<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KeywordData extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'country_id',
        'position',
        'search_volume',
        'clicks',
        'impression',
        'competition',
        'bid_rate_low',
        'bid_rate_high',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
