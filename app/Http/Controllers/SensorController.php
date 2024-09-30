<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class SensorController extends Controller
{
    public function subscribeToSensorData()
    {
        // Khởi tạo MQTT client
        $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'LaravelClient' . rand());

        $connectionSettings = (new ConnectionSettings)
            ->setUsername(env('MQTT_USERNAME'))
            ->setPassword(env('MQTT_PASSWORD'))
            ->setUseTls(false);

        try {
            $mqtt->connect($connectionSettings);
            \Log::info("Connected to MQTT broker");

            // Đăng ký nhận dữ liệu từ topic
            $topic = 'home/sensor_data'; // Địa chỉ topic bạn đang lắng nghe
            
            // Đăng ký callback cho việc xử lý dữ liệu
            $mqtt->subscribe($topic, function ($topic, $message) {
                $this->handleSensorData($message);
            }, 0); // QoS là 0

            // Vòng lặp lắng nghe
            while ($mqtt->loop(true)) {
                // Vòng lặp này sẽ duy trì việc lắng nghe tin nhắn
            }

        } catch (\Exception $e) {
            \Log::error("Error connecting to MQTT broker: " . $e->getMessage());
        }
    }

    private function handleSensorData($message)
    {
        // Chuyển đổi dữ liệu từ JSON
        $data = json_decode($message, true);

        if ($data) {
            $temperature = $data['temperature'] ?? null;
            $humidity = $data['humidity'] ?? null;
            $light = $data['light'] ?? null;

            // Lưu vào bảng sensor_data_history
            DB::table('sensor_data_history')->insert([
                'temperature' => $temperature,
                'humidity' => $humidity,
                'light' => $light,
                'received_at' => now(), // Thời gian nhận dữ liệu
            ]);

            // Ghi log
            \Log::info("Sensor data received: Temperature: $temperature, Humidity: $humidity, Light: $light");
        } else {
            \Log::error("Invalid sensor data format: $message");
        }
    }
}
