<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceToggle extends Model
{
    use HasFactory;
    protected $fillable = [
        'device_id',
        'device_name',
        'status',
        'toggled_at',
    ];

}