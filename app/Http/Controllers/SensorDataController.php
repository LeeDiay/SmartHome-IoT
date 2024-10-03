<?php

namespace App\Http\Controllers;

use App\Models\SensorDataHistory;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    /**
     * Display a listing of sensor data.
     *
     * Retrieves all data from the `sensor_data_history` table and sorts it by the received time. 
     * Data is paginated with 10 records per page.
     * @OA\Get(
     *     path="/sensor-data",
     *     summary="Get all sensor data",
     *     tags={"Sensor Data"},
     *     @OA\Response(
     *         response=200,
     *         description="A list of sensor data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1),
     *             @OA\Property(property="data", type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="temperature", type="number", example=22.5),
     *                     @OA\Property(property="humidity", type="number", example=60.0),
     *                     @OA\Property(property="light", type="number", example=350),
     *                     @OA\Property(property="received_at", type="string", example="2024-10-03T10:00:00"),
     *                 )
     *             ),
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function index()
    {
        // Retrieve all data from the sensor_data_history table and sort by received time
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate(10);
        
        // Return data to the view
        return view('sensor-data.index', compact('sensorData'));
    }

    /**
     * Get the latest sensor data.
     * @OA\Get(
     *     path="/sensor-data/latest",
     *     summary="Get the latest sensor data",
     *     tags={"Sensor Data"},
     *     @OA\Response(
     *         response=200,
     *         description="The latest sensor data",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="current_page", type="integer", example=1, description="Current page number"),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=3153, description="Unique identifier for the sensor data entry"),
     *                     @OA\Property(property="temperature", type="string", example="30.00", description="Current temperature reading in degrees Celsius as a string"),
     *                     @OA\Property(property="humidity", type="string", example="53.00", description="Current humidity reading in percentage as a string"),
     *                     @OA\Property(property="light", type="integer", example=2700, description="Current light level reading in lumens"),
     *                     @OA\Property(property="received_at", type="string", format="date-time", example="2024-10-02T16:11:37.000000Z", description="Timestamp when the data was received in ISO 8601 format")
     *                 )
     *             ),
     *             @OA\Property(property="first_page_url", type="string", example="http://127.0.0.1:8000/sensor-data/latest?page=1", description="URL of the first page"),
     *             @OA\Property(property="from", type="integer", example=1, description="Index of the first item on the current page"),
     *             @OA\Property(property="last_page", type="integer", example=68, description="Total number of pages"),
     *             @OA\Property(property="last_page_url", type="string", example="http://127.0.0.1:8000/sensor-data/latest?page=68", description="URL of the last page"),
     *             @OA\Property(property="links", type="array", 
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="url", type="string", nullable=true, example="http://127.0.0.1:8000/sensor-data/latest?page=2", description="URL of the page"),
     *                     @OA\Property(property="label", type="string", example="2", description="Label for the page link"),
     *                     @OA\Property(property="active", type="boolean", example=false, description="Indicates if this page link is active")
     *                 )
     *             ),
     *             @OA\Property(property="next_page_url", type="string", example="http://127.0.0.1:8000/sensor-data/latest?page=2", description="URL of the next page"),
     *             @OA\Property(property="path", type="string", example="http://127.0.0.1:8000/sensor-data/latest", description="Base URL of the request"),
     *             @OA\Property(property="per_page", type="integer", example=10, description="Number of items per page"),
     *             @OA\Property(property="prev_page_url", type="string", nullable=true, example=null, description="URL of the previous page"),
     *             @OA\Property(property="to", type="integer", example=10, description="Index of the last item on the current page"),
     *             @OA\Property(property="total", type="integer", example=671, description="Total number of items")
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function getLatestData(Request $request)
    {
        $sensorData = SensorDataHistory::orderBy('received_at', 'desc')->paginate(10);
        return response()->json($sensorData);
    }

    /**
     * Filter sensor data based on time range and attributes.
     * @OA\Post(
     *     path="/sensor-data/filter",
     *     summary="Filter sensor data",
     *     tags={"Sensor Data"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"start_date", "end_date"},
     *             @OA\Property(property="start_date", type="string", format="date", example="2024-10-01"),
     *             @OA\Property(property="end_date", type="string", format="date", example="2024-10-03"),
     *             @OA\Property(property="sort_by", type="array", @OA\Items(type="string", example="temperature")),
     *             @OA\Property(property="sort_order", type="string", example="asc"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Filtered sensor data",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="temperature", type="number", example=22.5),
     *                 @OA\Property(property="humidity", type="number", example=60.0),
     *                 @OA\Property(property="light", type="number", example=350),
     *                 @OA\Property(property="received_at", type="string", example="2024-10-03T10:00:00"),
     *             )
     *         )
     *     ),
     *     @OA\Response(response=500, description="Internal server error")
     * )
     */
    public function filterData(Request $request)
    {
        // Retrieve parameters from the request
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $sortBy = $request->input('sort_by'); // light, temperature, humidity
        $sortOrder = $request->input('sort_order', 'asc'); // ascending or descending (default is asc)

        // Create the initial query
        $query = SensorDataHistory::query();

        // Filter by date range (if provided)
        if ($startDate && $endDate) {
            $query->whereBetween('received_at', [$startDate, $endDate]);
        }

        // Filter by selected attributes
        if ($sortBy && is_array($sortBy)) {
            foreach ($sortBy as $column) {
                $query->orWhereNotNull($column); // Filter non-null values if needed, you can customize this condition
            }
        }

        // Add sorting by column and order (if provided)
        if ($sortBy) {
            $query->orderBy($sortBy[0], $sortOrder); // Sort by the first column in the array
        }

        // Retrieve the filtered data
        $filteredData = $query->paginate(10);

        // Return JSON for frontend processing
        return response()->json($filteredData);
    }
}