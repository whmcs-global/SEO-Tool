<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class keyword_label extends Model
{
    use HasFactory;

    protected $fillable = [
        'keyword_id',
        'label_id'
    ];
}
