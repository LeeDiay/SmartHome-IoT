<?php
namespace App\Http\Controllers;

use App\Models\DeviceToggle; // Model bảng device_toggles
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

        // Tạo truy vấn để lấy lịch sử bật/tắt từ bảng device_toggles
        $query = DeviceToggle::query();

        // Thêm điều kiện tìm kiếm theo thời gian nếu có
        if ($searchTime) {
            // Kiểm tra xem đầu vào có phải là ngày không
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $searchTime)) {
                // Nếu là ngày, lấy tất cả bản ghi trong ngày đó
                $startOfDay = Carbon::parse($searchTime)->startOfDay();
                $endOfDay = Carbon::parse($searchTime)->endOfDay();
                $query->whereBetween('toggled_at', [$startOfDay, $endOfDay]);
            } elseif (preg_match('/^\d{2}:\d{2}:\d{2}$/', $searchTime)) {
                // Nếu là giờ:phút:giây, lấy tất cả bản ghi có thời gian chính xác đó
                $query->where('toggled_at', 'LIKE', '%' . $searchTime . '%'); // So sánh theo giờ:phút:giây
            } elseif (preg_match('/^\d{2}:\d{2}$/', $searchTime)) {
                // Nếu là giờ:phút, lấy tất cả bản ghi có giờ và phút đó
                // Chỉ cần kiểm tra giờ và phút trong `toggled_at`
                $query->whereTime('toggled_at', Carbon::createFromFormat('H:i', $searchTime));
            }
        }

        // Sắp xếp và phân trang
        $deviceHistory = $query->orderBy('toggled_at', 'desc')->paginate($pageSize);

        // Truyền dữ liệu sang view
        return view('device-history.index', compact('deviceHistory', 'pageSize', 'searchTime'));
    }
}
