<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
class Backlinks extends Model
{
    use HasFactory;
    protected $fillable = [
        'date',
        'website_id',
        'user_id',
        'keyword_id',
        'website',
        'url',
        'backlink_source',
        'link_type',
        'spam_score',
        'anchor_text',
        'domain_authority',
        'page_authority',
        'contact_person',
        'notes_comments',
        'aproval_status',
        'approved_by',
        'reason',
        'email',
        'password',
        'login_url',
        'status',
    ];

    public function getWebsiteAttribute($value)
    {
        return str_replace(['http://', 'https://', 'www.'], '', $value);
    }

    protected $appends = ['created_by','email', 'keyword_value'];

    public function getEmailAttribute()
    {
        return $this->user ? $this->user->email : null;
    }

    public function getCreatedByAttribute()
    {
        return $this->user ? $this->user->name : null;
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function keyword()
    {
        return $this->belongsTo(Keyword::class);
    }

    public function getKeywordValueAttribute()
    {
        return $this->keyword()->first() ? $this->keyword()->first()->keyword : null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
