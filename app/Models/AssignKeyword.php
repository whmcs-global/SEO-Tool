<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignKeyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'user_id',
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
