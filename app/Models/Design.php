<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Design extends Model
{
    use HasFactory;

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

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getImageAttribute($value)
    {
        return [
            'thumbnail' => $this->getImagePath('thumbnail', $value),
            'large' => $this->getImagePath('large', $value),
            'original' => $this->getImagePath('original', $value),
        ];
    }

    protected function getImagePath($size, $image)
    {
        return Storage::disk($this->disk)->url("uploads/designs/{$size}/" . $image);
    }
}
