<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Devices extends Model
{
    use HasFactory;
    protected $fillable = [
        'android_id',
        'mac_address',
        'last_active_at',
        'status',
        'script',
    ];
}
