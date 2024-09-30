<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="sensor.data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg ">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Danh sách giá trị môi trường"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0">Bảng theo dõi các giá trị môi trường</h6>
                            <div class="ms-md-auto pe-md-3">
                                <div class="input-group input-group-outline">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#filterModal">Tìm kiếm</button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div id="searchInfo" class="alert alert-info" style="display: none;"></div>
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">NHIỆT ĐỘ</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ĐỘ ẨM</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">ÁNH SÁNG</th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">THỜI GIAN</th>
                                        </tr>
                                    </thead>
                                    <tbody id="table-body">
                                        @foreach($sensorData as $index => $data)
                                        <tr>
                                            <td>{{ $sensorData->firstItem() + $index }}</td>
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $data->temperature }}°C</h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $data->humidity }}%</h6>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $data->light }} Lux</h6>
                                                </div>
                                            </td>
                                            <td class="align-middle text-center text-sm">
                                                <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($data->received_at)->format('H:i:s d-m-Y') }}</p>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <!-- Hiển thị phân trang -->
                        <div class="d-flex justify-content-center">
                            {{ $sensorData->links('pagination::bootstrap-4') }} <!-- Hiển thị các liên kết phân trang -->
                        </div>
                    </div>
                </div>
            </div>
            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <!-- Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Lọc và Sắp xếp</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="sortOrder" class="form-label">Sắp xếp theo</label>
                        <select id="sortOrder" class="form-select">
                            <option value="asc">Tăng dần</option>
                            <option value="desc">Giảm dần</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="filterBy" class="form-label">Lọc theo</label>
                        <select id="filterBy" class="form-select" multiple>
                            <option value="light">Ánh sáng</option>
                            <option value="temperature">Nhiệt độ</option>
                            <option value="humidity">Độ ẩm</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                    <button type="button" class="btn btn-primary" id="confirmFilter">Xác nhận</button>
                </div>
            </div>
        </div>
    </div>

    <x-plugins></x-plugins>
</x-layout>

<script>
    let fetchInterval;

    function fetchLatestData() {
        fetch('/sensor-data/latest')
            .then(response => response.json())
            .then(data => {
                const tableBody = document.getElementById('table-body');
                const paginationContainer = document.getElementById('pagination-container'); // Đảm bảo có phần tử này trong HTML
                
                tableBody.innerHTML = ''; // Xóa nội dung cũ
                data.data.forEach((item, index) => {
                    const date = new Date(item.received_at);
                    const formattedDate = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')} ${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;

                    const row = `
                        <tr>
                            <td>${data.from + index}</td>
                            <td>
                                <div class="d-flex px-2 py-1">
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm">${item.temperature}°C</h6>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${item.humidity}%</h6>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${item.light} Lux</h6>
                                </div>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <p class="text-xs text-secondary mb-0">${formattedDate}</p>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });

                // Cập nhật phân trang
                paginationContainer.innerHTML = data.links.map(link => `<a href="${link.url}">${link.label}</a>`).join('');
            })
            .catch(error => console.error('Error fetching latest data:', error));
    }

    // Khởi tạo và gọi hàm cập nhật mỗi 2 giây
    fetchInterval = setInterval(fetchLatestData, 2000);

    document.getElementById('confirmFilter').addEventListener('click', function() {
        const sortOrder = document.getElementById('sortOrder').value;
        const filterBySelect = document.getElementById('filterBy');
        const selectedFilters = Array.from(filterBySelect.selectedOptions).map(option => option.value);
        
        // Hiển thị thông tin tìm kiếm
        document.getElementById('searchInfo').innerText = `Đang tìm kiếm theo: ${selectedFilters.join(', ')} (${sortOrder})`;
        document.getElementById('searchInfo').style.display = 'block';

        // Tạm dừng việc gọi hàm fetchLatestData
        clearInterval(fetchInterval);

        // Gửi yêu cầu lọc dữ liệu
        fetch('/sensor-data/filter', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}' // Thêm token CSRF
            },
            body: JSON.stringify({
                sort_by: selectedFilters, // Gửi danh sách lọc
                sort_order: sortOrder
            })
        })
        .then(response => response.json())
        .then(data => {
            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = ''; // Xóa nội dung cũ
            data.data.forEach((item, index) => {
                const date = new Date(item.received_at);
                const formattedDate = `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}:${String(date.getSeconds()).padStart(2, '0')} ${String(date.getDate()).padStart(2, '0')}-${String(date.getMonth() + 1).padStart(2, '0')}-${date.getFullYear()}`;

                const row = `
                    <tr>
                        <td>${data.from + index}</td>
                        <td>${item.temperature}°C</td>
                        <td>${item.humidity}%</td>
                        <td>${item.light} Lux</td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs text-secondary mb-0">${formattedDate}</p>
                        </td>
                    </tr>
                `;
                tableBody.insertAdjacentHTML('beforeend', row);
            });

            // Cập nhật phân trang (nếu cần)
            paginationContainer.innerHTML = data.links.map(link => `<a href="${link.url}">${link.label}</a>`).join('');
        })
        .catch(error => console.error('Error fetching filtered data:', error));

        // Đóng modal
        $('#filterModal').modal('hide');
    });

</script>
