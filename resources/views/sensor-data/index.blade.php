<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage="sensor.data"></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <x-navbars.navs.auth titlePage="Danh sách giá trị môi trường"></x-navbars.navs.auth>
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="d-flex justify-content-between align-items-center p-3">
                            <h6 class="mb-0">Bảng theo dõi các giá trị môi trường</h6>
                            <!-- Thêm link đến Material Icons -->
                            <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
                            <div class="d-flex align-items-center">
                                <!-- Ô nhập với icon tìm kiếm và padding cho phần nhập -->
                                <div class="input-group me-2">
                                    <input type="text" id="search" class="form-control border border-dark" placeholder="Nhập chuỗi tìm kiếm" onkeypress="if(event.key === 'Enter'){ applyFilter(); }" style="padding-left: 10px;">
                                </div>

                                <!-- Chọn kích thước trang -->
                                <div class="d-flex align-items-center justify-content-end px-4 mb-2">
                                    <div class="styled-select-wrapper">
                                        <select name="pageSize" id="pageSize" class="form-select styled-select w-auto" onchange="applyFilter()">
                                            <option value="10">10</option>
                                            <option value="20">20</option>
                                            <option value="50">50</option>
                                            <option value="100">100</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Nút Lọc với icon filter -->
                                <button class="btn btn-primary btn-sm" style="height: 50px; width: 100px;" data-bs-toggle="modal" data-bs-target="#filterModal">
                                    <span class="material-icons">filter_alt</span>
                                </button>

                                <!-- Nút Xóa bộ lọc với icon xóa -->
                                <button class="btn btn-secondary btn-sm ms-2" style="height: 50px; width: 100px;" onclick="clearFilters()">
                                    <span class="material-icons">clear</span>
                                </button>
                            </div>
                        </div>

                        <!-- Modal for filtering and sorting -->
                        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="filterModalLabel">Lọc và sắp xếp dữ liệu</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form id="filterForm">
                                            <!-- Dropdown filter -->
                                            <div class="mb-3">
                                                <label for="filter" class="form-label">Lọc theo</label>
                                                <select name="filter" id="filter" class="form-select styled-select">
                                                    <option value="" style="display: none;"></option>
                                                    <option value="temperature">Nhiệt độ</option>
                                                    <option value="humidity">Độ ẩm</option>
                                                    <option value="light">Ánh sáng</option>
                                                    <option value="received_at">Thời gian</option>
                                                </select>
                                            </div>

                                            <!-- Sort options -->
                                            <div class="mb-3">
                                                <label for="sort" class="form-label">Sắp xếp theo</label>
                                                <select name="sort" id="sort" class="form-select styled-select">
                                                    <option value="" style="display: none;"></option>
                                                    <option value="desc">Giảm dần</option>
                                                    <option value="asc">Tăng dần</option>
                                                </select>
                                            </div>


                                            <!-- Submit button inside modal -->
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal" onclick="applyFilter()">Áp dụng</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="card-body px-0 pb-2">
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
                                        <!-- Dữ liệu sẽ được chèn vào đây -->
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Pagination (Bootstrap 4 style) -->
                        <div class="d-flex justify-content-center my-3">
                            <nav aria-label="Page navigation">
                                <ul class="pagination" id="pagination-links">
                                    <!-- Các nút phân trang sẽ hiển thị ở đây -->
                                </ul>
                            </nav>
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
    let currentPage = 1; // Current page for pagination

    // Function to fetch data from the API and update the table
    async function fetchSensorData(page = 1) {
        try {
            // Get filter values from the form
            const search = document.getElementById('search').value;
            const filter = document.getElementById('filter').value;
            const sortOrder = document.getElementById('sort').value || 'desc';
            const pageSize = document.getElementById('pageSize').value;

            const queryString = `?page=${page}&pageSize=${pageSize}&search=${search}&filter=${filter}&sort_order=${sortOrder}`;
            const response = await fetch(`/sensor-data/filter${queryString}`, {
                headers: { 'Accept': 'application/json' }
            });
            const data = await response.json();

            const tableBody = document.getElementById('table-body');
            tableBody.innerHTML = ''; // Xóa nội dung bảng hiện tại

            // Kiểm tra xem có dữ liệu hay không
            if (data.data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="5" class="text-center">
                        <h6 class="mb-0 text-sm">Không tìm thấy dữ liệu</h6>
                    </td>
                `;
                tableBody.appendChild(row);
            } else {
                data.data.forEach((entry, index) => {
                    const row = document.createElement('tr');

                    row.innerHTML = `
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm" style="padding-left: 10px;">${data.from + index}</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex px-2 py-1">
                                <div class="d-flex flex-column justify-content-center">
                                    <h6 class="mb-0 text-sm">${entry.temperature}°C</h6>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">${entry.humidity}%</h6>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex flex-column justify-content-center">
                                <h6 class="mb-0 text-sm">${entry.light}</h6>
                            </div>
                        </td>
                        <td class="align-middle text-center text-sm">
                            <p class="text-xs text-secondary mb-0">
                                ${new Date(entry.received_at).toLocaleString('vi-VN', {
                                    hour: '2-digit',
                                    minute: '2-digit',
                                    second: '2-digit',
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                })}
                            </p>
                        </td>
                    `;

                    tableBody.appendChild(row);
                });
            }

            // Update pagination links
            updatePagination(data);
        } catch (error) {
            console.error('Error fetching sensor data:', error);
        }
    }


    // Function to update pagination links using Bootstrap 4
    function updatePagination(data) {
        const paginationLinks = document.getElementById('pagination-links');
        paginationLinks.innerHTML = ''; // Clear previous pagination

        const totalPages = data.last_page;
        const currentPage = data.current_page;
        const pageLimit = 7; // Number of pages to show around the current page
        let startPage, endPage;

        // Calculate start and end page
        if (totalPages <= pageLimit) {
            // If total pages is less than or equal to pageLimit, show all pages
            startPage = 1;
            endPage = totalPages;
        } else {
            // Calculate start and end page to show
            startPage = Math.max(1, currentPage - Math.floor(pageLimit / 2));
            endPage = Math.min(totalPages, startPage + pageLimit - 1);

            // Adjust if we're near the start
            if (startPage === 1) {
                endPage = Math.min(pageLimit, totalPages);
            }

            // Adjust if we're near the end
            if (endPage === totalPages) {
                startPage = Math.max(1, totalPages - pageLimit + 1);
            }
        }

        // Add "First" button
        if (currentPage > 1) {
            const firstButton = document.createElement('li');
            firstButton.className = 'page-item';
            firstButton.innerHTML = '<a class="page-link" onclick="changePage(1)">Đầu</a>';
            paginationLinks.appendChild(firstButton);
        }

        // Add page buttons
        for (let i = startPage; i <= endPage; i++) {
            const pageItem = document.createElement('li');
            pageItem.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageItem.innerHTML = `<a class="page-link" onclick="changePage(${i})">${i}</a>`;
            paginationLinks.appendChild(pageItem);
        }

        // Add "Last" button
        if (currentPage < totalPages) {
            const lastButton = document.createElement('li');
            lastButton.className = 'page-item';
            lastButton.innerHTML = `<a class="page-link" onclick="changePage(${totalPages})">Cuối</a>`;
            paginationLinks.appendChild(lastButton);
        }
    }

    // Function to change the page and fetch data
    function changePage(page) {
        currentPage = page;
        fetchSensorData(page);
    }

    // Function to apply filter and fetch data
    function applyFilter() {
        fetchSensorData(currentPage);
    }

    // Function to clear filters and reset form
    function clearFilters() {
        document.getElementById('search').value = '';
        document.getElementById('filter').selectedIndex = 0;
        document.getElementById('sort').selectedIndex = 0;
        document.getElementById('pageSize').value = '10';
        fetchSensorData(currentPage); // Fetch data with cleared filters
    }

    // Initial data fetch
    document.addEventListener('DOMContentLoaded', () => {
        fetchSensorData();
    });
</script>
<!-- Simple CSS for prettier dropdown -->
<style>
    .styled-select {
        border-radius: 5px;
        padding: 10px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        transition: border-color 0.3s, box-shadow 0.3s;
    }

    .styled-select:hover, .styled-select:focus {
        border-color: #007bff;
        box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
    }

    /* Optional: Adding some space between options */
    select option {
        padding: 10px;
    }
</style>