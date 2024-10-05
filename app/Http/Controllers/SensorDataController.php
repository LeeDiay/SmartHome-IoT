<?php

namespace App\Http\Controllers;

use App\Models\SensorDataHistory;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    public function index(Request $request)
    {
        // Set default values
        $pageSize = $request->query('pageSize', 10);
        
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate($pageSize);

        // Check if the request expects JSON response (for API calls)
        return $request->expectsJson() 
            ? response()->json($sensorData) 
            : view('sensor-data.index', compact('sensorData'));
    }

    public function getLatestData(Request $request)
    {
        $pageSize = $request->query('pageSize', 10);
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate($pageSize);

        return response()->json($sensorData);
    }

    public function filterData(Request $request)
    {
        $searchString = $request->input('search', '');
        $pageSize = $request->input('pageSize', 10);

        // Tạo truy vấn cơ bản
        $query = SensorDataHistory::query();

        // Áp dụng bộ lọc tìm kiếm nếu có
        if ($searchString) {
            $query->where(function ($q) use ($searchString) {
                $q->where('received_at', 'like', '%' . $searchString . '%')
                ->orWhere('temperature', 'like', '%' . $searchString . '%')
                ->orWhere('humidity', 'like', '%' . $searchString . '%')
                ->orWhere('light', 'like', '%' . $searchString . '%');
            });
        }

        // Lấy thông tin sắp xếp
        $sortOrder = $request->input('sort_order', 'asc');
        $sortBy = $request->input('sort_by', 'received_at');
        
        // Kiểm tra nếu có filter cụ thể và không có input tìm kiếm
        if ($request->filled('filter') && in_array($request->input('filter'), ['temperature', 'humidity', 'light', 'received_at'])) {
            $filterBy = $request->input('filter');

            // Nếu không có searchString, lọc theo filter
            if (!$searchString) {
                $query->orderBy($filterBy, $sortOrder); // Thêm điều kiện sắp xếp theo filter
            } else {
                // Nếu có searchString, lọc theo trường được chọn
                $query->where($filterBy, 'like', '%' . $searchString . '%');
            }
        }

        // Áp dụng sắp xếp và phân trang cuối cùng
        $sensorData = $query->orderBy($sortBy, $sortOrder)->paginate($pageSize);

        return response()->json($sensorData);
    }
    
}
