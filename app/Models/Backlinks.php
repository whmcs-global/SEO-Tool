<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder; // Use the correct Builder class
use Illuminate\Support\Facades\Auth;
class Backlinks extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'website',
        'url',
        'target_keyword',
        'backlink_source',
        'link_type',
        'anchor_text',
        'domain_authority',
        'page_authority',
        'contact_person',
        'notes_comments',
        'status',
    ];
    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope('website_id', function (Builder $builder) {
            if (Auth::check()) {
                $builder->where('website_id', Auth::user()->website_id);
            }
        });
    }
}
