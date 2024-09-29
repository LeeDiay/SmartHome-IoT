<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeviceHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'status',
        'toggled_at',
    ];

    protected $dates = ['toggled_at'];
}
