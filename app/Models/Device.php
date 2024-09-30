<?php

// app/Models/Device.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status', 'last_toggled_at'];

    // Bạn có thể cần thêm các thuộc tính khác nếu có
}
