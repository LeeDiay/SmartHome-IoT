<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="device-history"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Lịch sử thiết bị"></x-navbars.navs.auth>

        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                    <div class="card-header p-3 d-flex justify-content-between align-items-center">
                        <h6 class="font-weight-bold">Lịch sử bật/tắt thiết bị</h6>
                        <div class="d-flex align-items-center">
                            <form id="search-form" class="mb-0 me-3">
                                <div class="d-flex align-items-center">
                                    <input type="text" name="search_time" id="search_time" class="form-control border border-dark me-2" style="padding-left: 10px;" placeholder="Nhập từ khóa tìm kiếm">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="material-icons">search</i>
                                    </button>
                                </div>
                            </form>
                            <form id="page-size-form" class="mb-0">
                                <label for="page_size" class="form-label mb-0 me-2">Số bản ghi</label>
                                <select name="page_size" id="page_size" class="form-select form-select-lg d-inline-block w-auto border">
                                    <option value="10">10</option>
                                    <option value="20">20</option>
                                    <option value="30">30</option>
                                    <option value="50">50</option>
                                </select>
                            </form>
                        </div>
                    </div>

                        <div class="card-body">                           
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">TÊN THIẾT BỊ</th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">TRẠNG THÁI</th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">THỜI GIAN</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    <!-- Nội dung sẽ được cập nhật bằng JS -->
                                </tbody>
                            </table>

                            <div class="d-flex justify-content-center mt-4">
                                <nav aria-label="Pagination">
                                    <ul class="pagination" id="pagination-links">
                                        <!-- Nội dung phân trang sẽ được cập nhật bằng JS -->
                                    </ul>
                                </nav>
                            </div>
                        </div>
                    </div>      
                </div>     
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>

<script>
    let currentPage = 1; // Khởi tạo trang hiện tại

    document.addEventListener('DOMContentLoaded', function() {
        // Gửi AJAX request khi thay đổi số lượng bản ghi
        document.getElementById('page_size').addEventListener('change', function() {
            fetchDeviceHistory();
        });

        // Gửi AJAX request khi thực hiện tìm kiếm
        document.getElementById('search-form').addEventListener('submit', function(e) {
            e.preventDefault();
            fetchDeviceHistory();
        });

        // Lấy dữ liệu mặc định khi tải trang
        fetchDeviceHistory();
    });

    // Hàm để gửi request và cập nhật bảng
    function fetchDeviceHistory(page = 1) {
        currentPage = page; // Cập nhật currentPage
        const pageSize = document.getElementById('page_size').value;
        const searchTime = document.getElementById('search_time').value;

        fetch(`{{ route('device-history.index') }}?page_size=${pageSize}&search_time=${searchTime}&page=${page}`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log(data); 
            updateTable(data.deviceHistory);
            updatePagination(data.pagination);
        });
    }

    // Cập nhật bảng với dữ liệu nhận được
    function updateTable(deviceHistory) {
        const tableBody = document.getElementById('table-body');
        tableBody.innerHTML = ''; // Xóa nội dung trước đó

        // Kiểm tra nếu không có dữ liệu
        if (deviceHistory.length === 0) {
            const noDataRow = `<tr>
                                    <td colspan="4" class="text-center">
                                        <h6 class="mb-0 text-sm">Không tìm thấy dữ liệu</h6>
                                    </td>
                                </tr>`;
            tableBody.innerHTML += noDataRow; // Thêm hàng thông báo vào bảng
        } else {
            deviceHistory.forEach((item) => {
                const row = `<tr>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm" style="padding-left: 8px;">${item.id}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex px-2 py-1">
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm">${item.device_name}</h6>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">${item.status ? 'Bật' : 'Tắt'}</h6>
                                    </div>
                                </td>
                                <td class="align-middle text-center text-sm">
                                    <p class="text-xs text-secondary mb-0">
                                        ${new Date(item.toggled_at).toLocaleString('vi-VN', {
                                            hour: '2-digit',
                                            minute: '2-digit',
                                            second: '2-digit',
                                            day: '2-digit',
                                            month: '2-digit',
                                            year: 'numeric',
                                        })}
                                    </p>
                                </td>
                            </tr>`;
                tableBody.innerHTML += row; // Thêm hàng mới vào bảng
            });
        }
    }

    // Cập nhật liên kết phân trang
    function updatePagination(data) {
        const paginationLinks = document.getElementById('pagination-links');
        paginationLinks.innerHTML = ''; // Xóa phân trang trước đó

        const totalPages = data.last_page;
        const currentPage = data.current_page;
        const pageLimit = 7; // Số trang hiển thị quanh trang hiện tại
        let startPage, endPage;

        // Tính toán trang bắt đầu và kết thúc
        if (totalPages <= pageLimit) {
            startPage = 1;
            endPage = totalPages;
        } else {
            startPage = Math.max(1, currentPage - Math.floor(pageLimit / 2));
            endPage = Math.min(totalPages, startPage + pageLimit - 1);

            // Điều chỉnh nếu gần đầu
            if (startPage === 1) {
                endPage = Math.min(pageLimit, totalPages);
            }

            // Điều chỉnh nếu gần cuối
            if (endPage === totalPages) {
                startPage = Math.max(1, totalPages - pageLimit + 1);
            }
        }

        // Thêm nút "Đầu"
        if (currentPage > 1) {
            const firstButton = document.createElement('li');
            firstButton.className = 'page-item';
            firstButton.innerHTML = '<a class="page-link" onclick="changePage(1)">Đầu</a>';
            paginationLinks.appendChild(firstButton);
        }

        // Thêm nút trang
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageItem.innerHTML = `<a class="page-link" onclick="changePage(${i})">${i}</a>`;
            paginationLinks.appendChild(pageItem);
        }

        // Thêm nút "Cuối"
        if (currentPage < totalPages) {
            const lastButton = document.createElement('li');
            lastButton.className = 'page-item';
            lastButton.innerHTML = `<a class="page-link" onclick="changePage(${totalPages})">Cuối</a>`;
            paginationLinks.appendChild(lastButton);
        }
    }

    // Hàm để chuyển trang và lấy dữ liệu
    function changePage(page) {
        fetchDeviceHistory(page);
    }
</script>
