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
                                    <label class="form-label">Tìm kiếm</label>
                                    <input id="search-input" type="text" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                ID
                                                <span class="sort-btn" data-sort="id">▲▼</span>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                NHIỆT ĐỘ
                                                <span class="sort-btn" data-sort="temperature">▲▼</span>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ĐỘ ẨM
                                                <span class="sort-btn" data-sort="humidity">▲▼</span>
                                            </th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                ÁNH SÁNG
                                                <span class="sort-btn" data-sort="light">▲▼</span>
                                            </th>
                                            <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                THỜI GIAN
                                                <span class="sort-btn" data-sort="time">▲▼</span>
                                            </th>
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
    <x-plugins></x-plugins>
</x-layout>

<!-- JavaScript -->
<script>
    $(document).ready(function() {
        // Tìm kiếm
        $('#search-input').on('keyup', function() {
            var value = $(this).val().toLowerCase();
            $('#table-body tr').filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
            });
        });

        // Sắp xếp
        $('.sort-btn').on('click', function() {
            var rows = $('#table-body tr').get();
            var sortColumn = $(this).data('sort');
            var sortOrder = $(this).data('order') === 'asc' ? 'desc' : 'asc';
            $(this).data('order', sortOrder);

            rows.sort(function(a, b) {
                var A = getCellValue(a, sortColumn);
                var B = getCellValue(b, sortColumn);

                if ($.isNumeric(A) && $.isNumeric(B)) {
                    return sortOrder === 'asc' ? A - B : B - A;
                } else {
                    return sortOrder === 'asc' ? A.localeCompare(B) : B.localeCompare(A);
                }
            });

            $.each(rows, function(index, row) {
                $('#table-body').append(row);
            });
        });

        function getCellValue(row, sortColumn) {
            var cellIndex;
            switch (sortColumn) {
                case 'id':
                    cellIndex = 0;
                    break;
                case 'temperature':
                    cellIndex = 1;
                    break;
                case 'humidity':
                    cellIndex = 2;
                    break;
                case 'light':
                    cellIndex = 3;
                    break;
                case 'time':
                    cellIndex = 4;
                    break;
            }
            return $(row).children('td').eq(cellIndex).text().trim();
        }
    });
</script>

<script>
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
                                    <h6 class="mb-0 text-sm">${item.light}</h6>
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

    // Gọi hàm cập nhật mỗi 2 giây
    setInterval(fetchLatestData, 2000);
</script>

