<?php

namespace App\Http\Controllers;

use App\Models\SensorDataHistory;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    
    public function index()
    {
        // Lấy tất cả dữ liệu từ bảng sensor_data_history và sắp xếp theo thời gian nhận
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate(10);
        
        // Trả dữ liệu về view
        return view('sensor-data.index', compact('sensorData'));
    }

    public function getLatestData(Request $request)
    {
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate(10);
        return response()->json($sensorData);
    }

    public function filterData(Request $request)
    {
        // Lọc dữ liệu dựa trên các tham số truyền vào từ request (ví dụ khoảng thời gian)
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Lọc dữ liệu trong khoảng thời gian được chỉ định
        $filteredData = SensorDataHistory::whereBetween('received_at', [$startDate, $endDate])->get();

        // Trả về view (hoặc JSON)
        return view('sensor_data.filtered', compact('filteredData'));

        // Hoặc trả về JSON (nếu sử dụng cho API)
        // return response()->json($filteredData);
    }
}
