<?php

namespace App\Http\Controllers;

use App\Models\DeviceToggle; // Model for the device_toggles table
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeviceHistoryController extends Controller
{
    public function index(Request $request)
    {
        // Lấy kích thước trang từ yêu cầu, mặc định là 10
        $pageSize = $request->input('page_size', 10);
        
        // Lấy thời gian tìm kiếm từ yêu cầu
        $searchTime = $request->input('search_time');

        // Tạo query để lấy lịch sử bật/tắt từ bảng device_toggles
        $query = DeviceToggle::query();

        // Thêm điều kiện tìm kiếm theo thời gian nếu được cung cấp
        if ($searchTime) {
            // Kiểm tra nếu đầu vào là định dạng DD/MM/YYYY
            if (preg_match('/^\d{2}\/\d{2}\/\d{4}$/', $searchTime)) {
                // Chuyển đổi sang định dạng YYYY-MM-DD
                $date = Carbon::createFromFormat('d/m/Y', $searchTime);
                if ($date) {
                    // Tìm kiếm theo thời gian chính xác
                    $query->whereDate('toggled_at', $date);
                }
            } elseif (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTime)) {
                // Nếu là định dạng YYYY-MM-DD, tìm kiếm theo thời gian
                $query->whereDate('toggled_at', $searchTime);
            } elseif (preg_match('/^\d{2}:\d{2}:\d{2}$/', $searchTime)) {
                // Nếu là thời gian HH:MM:SS, tìm các bản ghi chính xác theo thời gian đó
                $query->where('toggled_at', 'LIKE', '%' . $searchTime . '%');
            } elseif (preg_match('/^\d{2}:\d{2}$/', $searchTime)) {
                // Nếu là thời gian HH:MM, tìm các bản ghi theo giờ và phút
                $query->where('toggled_at', 'LIKE', '%' . $searchTime . '%'); // Tìm kiếm theo giờ và phút
            } elseif (preg_match('/^\d{2}\/\d{2}\/\d{4} \d{2}:\d{2}(:\d{2})?$/', $searchTime)) {
                // Nếu là định dạng DD/MM/YYYY HH:MM hoặc DD/MM/YYYY HH:MM:SS
                $datetime = Carbon::createFromFormat('d/m/Y H:i:s', $searchTime);
                if (!$datetime) {
                    $datetime = Carbon::createFromFormat('d/m/Y H:i', $searchTime);
                }
                if ($datetime) {
                    $query->where('toggled_at', 'LIKE', '%' . $datetime->format('Y-m-d H:i') . '%');
                }
            } elseif (preg_match('/^\d{2}\/\d{2}$/', $searchTime)) {
                // Nếu là định dạng DD/MM, chuyển đổi sang tháng năm
                $monthDay = explode('/', $searchTime);
                if (count($monthDay) === 2) {
                    $day = $monthDay[0];
                    $month = $monthDay[1];
                    // Lấy năm hiện tại
                    $currentYear = now()->year;
                    // Tạo một ngày từ định dạng DD/MM
                    $date = Carbon::createFromFormat('d/m/Y', "{$day}/{$month}/{$currentYear}");
                    if ($date) {
                        // Tìm kiếm theo ngày
                        $query->whereDate('toggled_at', $date);
                    }
                }
            }
        }

        // Sắp xếp theo cột toggled_at và phân trang kết quả
        $deviceHistory = $query->orderBy('toggled_at', 'desc')->paginate($pageSize);
        
        // Tính toán ID cho dữ liệu trả về dựa trên trang hiện tại
        $currentPage = $deviceHistory->currentPage(); // Lấy trang hiện tại
        $offset = ($currentPage - 1) * $pageSize; // Tính toán độ lệch cho ID

        // Cập nhật ID cho dữ liệu trả về
        $deviceHistory->getCollection()->transform(function ($item, $key) use ($offset) {
            // Cập nhật ID thành $key + $offset + 1
            $item->id = $key + $offset + 1;
            return $item;
        });

        // Kiểm tra nếu yêu cầu muốn nhận dữ liệu dưới dạng JSON
        if ($request->expectsJson()) {
            // Trả về dữ liệu JSON
            return response()->json([
                'deviceHistory' => $deviceHistory->items(), // Trả về tất cả dữ liệu
                'pagination' => [
                    'total' => $deviceHistory->total(),
                    'per_page' => $deviceHistory->perPage(),
                    'current_page' => $deviceHistory->currentPage(),
                    'last_page' => $deviceHistory->lastPage(),
                    'from' => $deviceHistory->firstItem(),
                    'to' => $deviceHistory->lastItem(),
                ],
                'pageSize' => $pageSize,
                'searchTime' => $searchTime
            ]);
        }

        // Nếu không phải JSON, trả về view
        return view('device-history.index', compact('deviceHistory', 'pageSize', 'searchTime'));
    }

}
