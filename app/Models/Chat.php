<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Chat extends Model
{
    use HasFactory;

    public function participants()
    {
        return $this->belongsTo(User::class, 'participants');
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function getLatestMessageAttribute()
    {
        return $this->messages()->latest()->first();
    }

    public function isUnreadForUser($useId)
    {
        return (bool)$this->messages()->whereNull('last_read')->where('user_id', '<>', $useId)->count();
    }

    public function markAsReadForUser($useId)
    {
        return $this->messages()->whereNull('last_read')->where('user_id', '<>', $useId)->update([
            'last_read' => Carbon::now()
        ]);

    }

}
