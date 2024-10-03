<?php
namespace App\Http\Controllers;

use App\Models\DeviceToggle; // Model for the device_toggles table
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeviceHistoryController extends Controller
{
    /**
     * Display a paginated list of device toggle history records with optional search functionality.
     * 
     * This method allows users to view device toggle history and filter by specific date or time.
     * It supports pagination and searches by exact or partial time matches.
     *
     * @OA\Get(
     *     path="/device-history",
     *     summary="Get device toggle history",
     *     tags={"Device History"},
     *     @OA\Parameter(
     *         name="page_size",
     *         in="query",
     *         description="The number of records to return per page (default is 10)",
     *         required=false,
     *         @OA\Schema(
     *             type="integer",
     *             example=10
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search_time",
     *         in="query",
     *         description="Search for toggles by date (YYYY-MM-DD), time (HH:MM:SS), or time (HH:MM)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             example="2024-10-02"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of device toggle history",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="deviceHistory",
     *                 type="array",
     *                 @OA\Items(
     *                     @OA\Property(property="device_id", type="integer", example=1),
     *                     @OA\Property(property="device_name", type="string", example="Quạt"),
     *                     @OA\Property(property="status", type="boolean", example=true),
     *                     @OA\Property(property="toggled_at", type="string", example="15:32:10 02-10-2024")
     *                 )
     *             ),
     *             @OA\Property(property="pageSize", type="integer", example=10),
     *             @OA\Property(property="searchTime", type="string", example="2024-10-02")
     *         )
     *     )
     * )
     * 
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // Get the page size from the request, default is 10
        $pageSize = $request->input('page_size', 10);
        
        // Get the search time from the request
        $searchTime = $request->input('search_time');

        // Create a query to fetch toggle history from the device_toggles table
        $query = DeviceToggle::query();

        // Add a time-based search condition if provided
        if ($searchTime) {
            // Check if input is a valid date
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTime)) {
                // If it's a date, fetch all records for that day
                $startOfDay = Carbon::parse($searchTime)->startOfDay();
                $endOfDay = Carbon::parse($searchTime)->endOfDay();
                $query->whereBetween('toggled_at', [$startOfDay, $endOfDay]);
            } elseif (preg_match('/^\d{2}:\d{2}:\d{2}$/', $searchTime)) {
                // If it's a time in HH:MM:SS, fetch exact matches
                $query->where('toggled_at', 'LIKE', '%' . $searchTime . '%');
            } elseif (preg_match('/^\d{2}:\d{2}$/', $searchTime)) {
                // If it's a time in HH:MM, fetch records with that hour and minute
                $query->whereTime('toggled_at', Carbon::createFromFormat('H:i', $searchTime));
            }
        }

        // Sort by the toggled_at column and paginate the results
        $deviceHistory = $query->orderBy('toggled_at', 'desc')->paginate($pageSize);

        // Pass the data to the view
        return view('device-history.index', compact('deviceHistory', 'pageSize', 'searchTime'));
    }
}
