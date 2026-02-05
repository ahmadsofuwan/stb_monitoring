<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicFile extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'slug',
    ];

    public function getPublicUrlAttribute()
    {
        return url('/f/' . $this->slug);
    }
}
