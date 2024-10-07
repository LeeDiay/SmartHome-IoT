<?php

namespace App\Http\Controllers;

use App\Models\SensorDataHistory;
use Illuminate\Http\Request;
use Carbon\Carbon;

class SensorDataController extends Controller
{
    /**
     * @OA\Get(
     *     path="/sensor-data",
     *     summary="Get sensor data history",
     *     description="Retrieve the list of sensor data with pagination",
     *     operationId="getSensorDataHistory",
     *     tags={"Sensor Data"},
     *     @OA\Parameter(
     *         name="pageSize",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 description="List of sensor data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="temperature", type="number", example=25.5),
     *                     @OA\Property(property="humidity", type="number", example=60.3),
     *                     @OA\Property(property="light", type="integer", example=800),
     *                     @OA\Property(property="received_at", type="string", example="2023-10-05 12:34:56")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 description="Pagination details",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cannot find data from database",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Get(
     *     path="/sensor-data/latest",
     *     summary="Get the latest sensor data",
     *     description="Retrieve the latest sensor data with pagination",
     *     operationId="getLatestSensorData",
     *     tags={"Sensor Data"},
     *     @OA\Parameter(
     *         name="pageSize",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(
     *                 property="data",
     *                 description="Latest sensor data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="temperature", type="number", example=26.0),
     *                     @OA\Property(property="humidity", type="number", example=65.0),
     *                     @OA\Property(property="light", type="integer", example=700),
     *                     @OA\Property(property="received_at", type="string", example="2023-10-05 13:00:00")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cannot find data from database",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function getLatestData(Request $request)
    {
        $pageSize = $request->query('pageSize', 10);
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate($pageSize);

        return response()->json($sensorData);
    }

    /**
     * @OA\Get(
     *     path="/sensor-data/filter",
     *     summary="Filter sensor data",
     *     description="Retrieve filtered sensor data based on search criteria and sorting options",
     *     operationId="filterSensorData",
     *     tags={"Sensor Data"},
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search string for filtering by date, temperature, humidity, or light",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="25.5"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="pageSize",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sorting order: 'asc' or 'desc'",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"},
     *             example="asc"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Field to sort by: 'received_at', 'temperature', 'humidity', or 'light'",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="received_at"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="filter",
     *         in="query",
     *         description="Field to filter by: 'temperature', 'humidity', 'light', or 'received_at'",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"temperature", "humidity", "light", "received_at"},
     *             example="temperature"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 description="Filtered sensor data",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="temperature", type="number", example=25.5),
     *                     @OA\Property(property="humidity", type="number", example=60.3),
     *                     @OA\Property(property="light", type="integer", example=800),
     *                     @OA\Property(property="received_at", type="string", example="2023-10-05 12:34:56")
     *                 )
     *             ),
     *             @OA\Property(
     *                 property="pagination",
     *                 description="Pagination details",
     *                 @OA\Property(property="total", type="integer", example=100),
     *                 @OA\Property(property="per_page", type="integer", example=10),
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(property="last_page", type="integer", example=10),
     *                 @OA\Property(property="from", type="integer", example=1),
     *                 @OA\Property(property="to", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Cannot find data from database",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Data not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Internal server error")
     *         )
     *     )
     * )
     */
    public function filterData(Request $request)
    {
        $searchString = $request->input('search', '');
        $pageSize = $request->input('pageSize', 10);

        // Tạo truy vấn cơ bản
        $query = SensorDataHistory::query();

        // Áp dụng bộ lọc tìm kiếm nếu có
        if ($searchString) {
            $query->where(function ($q) use ($searchString) {
                // Chuyển đổi chuỗi tìm kiếm thành định dạng Carbon nếu có thể
                try {
                    // Kiểm tra định dạng thời gian
                    $dateTime = Carbon::createFromFormat('H:i:s d/m/Y', $searchString);
                    // Nếu thành công, thêm điều kiện cho trường received_at
                    if ($dateTime) {
                        $q->orWhere('received_at', '=', $dateTime);
                    }
                } catch (\Exception $e) {
                    // Nếu không thành công, kiểm tra xem có thể là ngày không
                    try {
                        $date = Carbon::createFromFormat('d/m/Y', $searchString);
                        // Nếu thành công, thêm điều kiện cho trường received_at (bỏ qua thời gian)
                        if ($date) {
                            $q->orWhereDate('received_at', '=', $date);
                        }
                    } catch (\Exception $e) {
                        $q->where('received_at', 'like', '%' . $searchString . '%')
                        ->orWhere('temperature', 'like', '%' . $searchString . '%')
                        ->orWhere('humidity', 'like', '%' . $searchString . '%')
                        ->orWhere('light', 'like', '%' . $searchString . '%');
                    }
                }
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
