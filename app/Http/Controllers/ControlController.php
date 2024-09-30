<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;
use App\Models\DeviceToggle;
use Carbon\Carbon;
use PhpMqtt\Client\MqttClient;
use PhpMqtt\Client\ConnectionSettings;

class ControlController extends Controller
{
    public function toggleDevice(Request $request)
    {
        $device = Device::find($request->device_id);
        if ($device) {
            // Lưu trạng thái mới
            $device->status = !$device->status;
            $device->last_toggled_at = now();
            $device->save();

            //Tạo mới 1 bản ghi vào lịch sử bật/tắt
            DeviceToggle::create([
                'device_id' => $device->id,
                'device_name' => $device->name, // Lưu tên thiết bị
                'status' => $device->status, // Trạng thái mới
                'toggled_at' => now(), // Thời gian bật/tắt
            ]);

            // Khởi tạo kết nối MQTT
            $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'LaravelClient' . rand());

            \Log::info("Trying to connect to MQTT broker...");

            // Cấu hình kết nối
            $connectionSettings = (new ConnectionSettings)
                ->setUsername(env('MQTT_USERNAME'))
                ->setPassword(env('MQTT_PASSWORD'))
                ->setUseTls(true); // Nếu bạn đang kết nối qua TLS

            try {
                // Kết nối đến broker
                $mqtt->connect($connectionSettings);
                \Log::info("Connected to MQTT broker");

                // Xác định topic dựa trên ID của thiết bị
                $topic = "home/led" . $device->id; // Ví dụ: "home/led1", "home/led2",...

                // Xác định tin nhắn dựa trên trạng thái mới
                $message = $device->status ? "on" : "off"; // Nếu thiết bị bật thì gửi "on", ngược lại gửi "off"

                // Gửi tin nhắn đến topic MQTT
                if (!$mqtt->publish($topic, $message, 0)) {
                    \Log::info("Message published to $topic: " . $message);
                } else {
                    \Log::error("Failed to publish message to $topic");
                }
                
                $mqtt->disconnect(); // Ngắt kết nối sau khi gửi tin nhắn
            } catch (\Exception $e) {
                \Log::error("Error connecting to MQTT broker: " . $e->getMessage());
            }

            return response()->json([
                'status' => 'success',
                'device' => [
                    'name' => $device->name,
                    'status' => $device->status,
                    'last_toggle_at' => $device->last_toggled_at->format('H:i:s d-m-Y')
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Device not found'
        ]);
    }
}
