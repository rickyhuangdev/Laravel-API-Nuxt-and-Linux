<?php

namespace App\Models;

use App\Models\Traits\Likeable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Cviebrock\EloquentTaggable\Taggable;

class Design extends Model
{
    use HasFactory, Taggable, Likeable;

    protected $fillable = [
        'user_id',
        'image',
        'title',
        'description',
        'slug',
        'close_to_comment',
        'is_live',
        'upload_successfully',
        'disk'
    ];
    protected $appends = [
        'handle_image'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function comments()
    {
        return $this->morphMany(Comment::class, 'commentable')->orderBy('created_at', 'asc');
    }

    public function getHandleImageAttribute()
    {

        return [
            'thumbnail' => $this->getImagePath('thumbnail', $this->image),
            'large' => $this->getImagePath('large', $this->image),
            'original' => $this->getImagePath('original', $this->image),
        ];
    }

    protected function getImagePath($size, $image)
    {

        return Storage::disk($this->disk)->url("uploads/designs/{$size}/" . $image);
    }
}
