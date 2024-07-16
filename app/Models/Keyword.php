<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = ['user_id','website_id','keyword','ip_address'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function website()
    {
        return $this->belongsTo(Website::class);
    }

    public function labels()
    {
        return $this->belongsToMany(Label::class, 'keyword_labels', 'keyword_id', 'label_id');
    }

    public function scopeFilterByLabels(Builder $query, array $labelIds)
    {
        return $query->whereHas('labels', function (Builder $query) use ($labelIds) {
            $query->whereIn('labels.id', $labelIds);
        });
    }

    public function scopeForUserAndWebsite(Builder $query, $websiteId)
    {
        return $query->where('website_id', $websiteId);
    }

    public function keywordData()
    {
        return $this->hasMany(KeywordData::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'assign_keywords', 'keyword_id', 'user_id');
    }

    public function scopeCreatedByUserOrAssignedByAdmin(Builder $query, $userId)
    {
        return $query->where(function ($query) use ($userId) {
            $query->where('user_id', $userId)
                ->orWhereNull('user_id');
        })->with('assignedUsers');
    }

    public function assignedByUser($userId)
    {
        return $this->belongsToMany(User::class, 'assign_keywords', 'keyword_id', 'user_id')
                    ->wherePivot('user_id', $userId);
    }

}
