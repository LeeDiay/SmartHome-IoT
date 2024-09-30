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
        // Lấy các tham số từ request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sortBy = $request->input('sort_by'); // ánh sáng, nhiệt độ, độ ẩm
        $sortOrder = $request->input('sort_order', 'asc'); // tăng dần hoặc giảm dần (mặc định là asc)

        // Tạo query ban đầu
        $query = SensorDataHistory::query();

        // Lọc theo khoảng thời gian (nếu có)
        if ($startDate && $endDate) {
            $query->whereBetween('received_at', [$startDate, $endDate]);
        }

        // Lọc theo các thuộc tính đã chọn
        if ($sortBy && is_array($sortBy)) {
            foreach ($sortBy as $column) {
                $query->orWhereNotNull($column); // Lọc không null nếu cần, bạn có thể tùy chỉnh điều kiện này
            }
        }

        // Thêm sắp xếp theo cột và thứ tự (nếu có)
        if ($sortBy) {
            $query->orderBy($sortBy[0], $sortOrder); // Sắp xếp theo cột đầu tiên trong mảng
        }

        // Lấy dữ liệu đã lọc
        $filteredData = $query->paginate(10);

        // Trả về JSON cho frontend xử lý
        return response()->json($filteredData);
    }


}
