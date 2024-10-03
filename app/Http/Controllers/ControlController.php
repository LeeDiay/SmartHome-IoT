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
    /**
     * Toggle the status of a device.
     *
     * This endpoint allows toggling the status of a device (on/off) and sends
     * a message to the MQTT broker to reflect the updated status.
     *
     * @OA\Post(
     *     path="/control/toggle-device",
     *     summary="Toggle device status",
     *     tags={"Device Controll"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"device_id"},
     *             @OA\Property(property="device_id", type="integer", example=1, description="The ID of the device to toggle")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Device toggled successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="device", type="object",
     *                 @OA\Property(property="name", type="string", example="Quạt"),
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="last_toggle_at", type="string", example="15:32:10 02-10-2024")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Device not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Device not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Failed to connect to MQTT broker",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Failed to connect to MQTT broker")
     *         )
     *     )
     * )
     */
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
                'device_name' => $device->name,
                'status' => $device->status,
                'toggled_at' => now(),
            ]);

            // Khởi tạo kết nối MQTT
            $mqtt = new MqttClient(env('MQTT_HOST'), env('MQTT_PORT'), 'LaravelClient' . rand());

            \Log::info("Trying to connect to MQTT broker...");

            // Cấu hình kết nối
            $connectionSettings = (new ConnectionSettings)
                ->setUsername(env('MQTT_USERNAME'))
                ->setPassword(env('MQTT_PASSWORD'))
                ->setUseTls(false);

            try {
                $mqtt->connect($connectionSettings);
                \Log::info("Connected to MQTT broker");

                // Xác định topic và gửi tin nhắn
                $topic = "home/led" . $device->id;
                $message = $device->status ? "on" : "off";

                if (!$mqtt->publish($topic, $message, 0)) {
                    \Log::info("Message published to $topic: " . $message);
                } else {
                    \Log::error("Failed to publish message to $topic");
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Failed to publish message'
                    ], 500);
                }

                $mqtt->disconnect();
            } catch (\Exception $e) {
                \Log::error("Error connecting to MQTT broker: " . $e->getMessage());
                return response()->json([
                    'status' => 'error',
                    'message' => 'Failed to connect to MQTT broker'
                ], 500);
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
        ], 404);
    }
}
