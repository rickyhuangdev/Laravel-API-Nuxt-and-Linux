<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'owner_id',
        'slug'
    ];

    protected static function boot()
    {
        parent::boot();
        static::created(function ($team) {
//            auth()->user()->teams()->attach($team->id);
            $team->members()->attach(auth()->id());
        });
        static::deleting(function ($team) {
//            auth()->user()->teams()->attach($team->id);
            $team->members()->sync([]);
        });
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function designs()
    {
        return $this->hasMany(Design::class);
    }

    public function hasUser(User $user)
    {
        return (bool)$this->members()->where('user_id', $user)->first();
    }

    public function invitation()
    {
        return $this->hasMany(Invitation::class);
    }

    public function hasPendingInvite($email)
    {
        return (bool)$this->invitation()->where('recipient_email', $email)->count();
    }
}
