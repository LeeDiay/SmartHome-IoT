<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SensorDataHistory extends Model
{
    use HasFactory;

    // Tên bảng tương ứng trong database
    protected $table = 'sensor_data_history';

    // Các trường được phép fill
    protected $fillable = [
        'temperature',
        'humidity',
        'light',
        'received_at'
    ];

    // Nếu bạn không muốn sử dụng các trường 'created_at' và 'updated_at', có thể bỏ qua chúng
    public $timestamps = true;

    // Định nghĩa kiểu dữ liệu cho các trường
    protected $casts = [
        'temperature' => 'decimal:2',
        'humidity' => 'decimal:2',
        'light' => 'integer',
        'received_at' => 'datetime',
    ];
}

