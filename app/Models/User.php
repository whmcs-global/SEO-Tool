<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    use HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'parent_id',
        'name',
        'email',
        'password',
        'country_id',
        'website_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    // if website_id is null then check user_project and first website_id
    public function getWebsiteIdAttribute($value)
    {
        if ($value) {
            return $value;
        }
        $website = $this->User_project()->first();
        return $website ? $website->id : null;
    }

    public function projectconfig_status()
    {
        return AdminSetting::where('website_id', $this->website_id)->count() == 2;
    }

    public function User_project()
    {
        return $this->hasMany(User_project::class);
    }

    public function keywords()
    {
        return $this->hasMany(Keyword::class);
    }

    public function website()
    {
        return $this->hasOne(Website::class);
    }

    public function backlinks()
    {
        return $this->hasMany(Backlinks::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function parent()
    {
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function getParentNameAttribute()
    {
        return $this->parent ? $this->parent->name : null;
    }

    // get that user that user assinged current website
    public function getcurrentwebsiteuser()
    {
        return User_project::where('website_id', $this->website_id)->get();
    }

    public function getCurrentProject()
    {
        return Website::where('id', $this->website_id)->first();
    }
}
