<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\SensorController;

class SensorSubscriber extends Command
{
    protected $signature = 'mqtt:sensor-subscribe';
    protected $description = 'Subscribe to MQTT topics for sensor data and handle messages';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        // Khởi chạy MQTT subscriber để lắng nghe dữ liệu cảm biến
        $controller = new SensorController();
        $controller->subscribeToSensorData();
    }
}
