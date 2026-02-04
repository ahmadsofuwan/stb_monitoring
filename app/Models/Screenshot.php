<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id',
        'filename',
    ];

    public function device()
    {
        return $this->belongsTo(Devices::class, 'device_id');
    }
}
