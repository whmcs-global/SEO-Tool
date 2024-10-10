<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Label extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    public function keywords()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_labels', 'label_id', 'keyword_id');
    }

    public function keywordsCurrentWebsite()
    {
        return $this->belongsToMany(Keyword::class, 'keyword_labels', 'label_id', 'keyword_id')
            ->where('website_id', auth()->user()->website_id);
    }

    public function getKeywordCountAttribute()
    {
        return $this->keywordsCurrentWebsite()->count();
    }

}
