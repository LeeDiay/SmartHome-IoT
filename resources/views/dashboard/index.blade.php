<x-layout bodyClass="g-sidenav-show bg-gray-200">
    <x-navbars.sidebar activePage='dashboard'></x-navbars.sidebar>
    <main class="main-content position-relative max-height-vh-100 h-100 border-radius-lg">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage="Trang chủ"></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <!-- Sử dụng col để thu nhỏ box và đảm bảo chúng vừa với màn hình -->
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card" id="temperature-card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">thermostat</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Nhiệt độ</p>
                                <h4 class="mb-0 total-users-count" id="temperature-value">19°C</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3"></div>
                    </div>
                </div>

                @if (Auth::user()->level == 'Admin')
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card" id="humidity-card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-success shadow-success text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">water_drop</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Độ ẩm</p>
                                <h4 class="mb-0 new-users-count" id="humidity-value">60%</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3"></div>
                    </div>
                </div>
                @endif

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card" id="light-card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">light_mode</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Ánh sáng</p>
                                <h4 class="mb-0 total-exercises-count" id="light-value">400 lux</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3"></div>
                    </div>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-xl-0 mb-4">
                    <div class="card">
                        <div class="card-header p-3 pt-2">
                            <div class="icon icon-lg icon-shape bg-gradient-info shadow-info text-center border-radius-xl mt-n4 position-absolute">
                                <i class="material-icons opacity-10">schedule</i>
                            </div>
                            <div class="text-end pt-1">
                                <p class="text-sm mb-0 text-capitalize">Thời gian thực</p>
                                <h4 class="mb-0" id="realTimeClock">--:--:--</h4>
                            </div>
                        </div>
                        <hr class="dark horizontal my-0">
                        <div class="card-footer p-3"></div>
                    </div>
                </div>
            </div>

            <!-- Sử dụng flexbox để sắp xếp biểu đồ và box điều khiển thiết bị -->
            <div class="row mt-5 d-flex">
                <!-- Biểu đồ hiển thị giá trị môi trường -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header p-3">
                            <h6>Biểu đồ các giá trị môi trường</h6>
                        </div>
                        <div class="card-body d-flex justify-content-center">
                            <div style="width: 100%; max-width: 800px; height: 400px;">
                                <canvas id="environmentChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Box điều khiển thiết bị nằm bên phải -->
                <div class="col-md-4">
                    <div class="row">
                        @foreach($devices as $device)
                        <div class="col-lg-6 col-md-12 mb-3">
                            <div class="card">
                                <div class="card-header p-3 text-center">
                                    <h5>{{ $device->name }}</h5>
                                    <p class="device-status">{{ $device->status ? 'Thiết bị đang bật' : 'Thiết bị đang tắt' }}</p>
                                </div>
                                <div class="card-body text-center">
                                    <div class="device-icon mb-3">
                                        @if($device->name == 'Quạt')
                                            <img src="{{ asset('assets/img/fan.png') }}" class="device-img {{ $device->status ? 'spin' : '' }}" alt="Fan">
                                        @elseif($device->name == 'Điều hòa')
                                            <img id="ac" src="{{ asset($device->status ? 'assets/img/ac-on.png' : 'assets/img/ac.png') }}" alt="AC">
                                        @elseif($device->name == 'Đèn')
                                            <img id="light" src="{{ asset($device->status ? 'assets/img/lightbulb-fill.png' : 'assets/img/lightbulb.png') }}" alt="Lightbulb">
                                        @endif
                                    </div>
                                    <label class="toggle-switch">
                                        <input type="checkbox" class="toggle-switch-checkbox" {{ $device->status ? 'checked' : '' }} data-id="{{ $device->id }}">
                                        <span class="toggle-switch-slider"></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <x-footers.auth></x-footers.auth>
        </div>
    </main>
    <x-plugins></x-plugins>
</x-layout>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        $(document).ready(function() {
            // Dữ liệu mẫu cho biểu đồ
            var sampleData = {
                timestamps: ["08:00", "09:00", "10:00", "11:00", "12:00", "13:00", "14:00"],
                temperatureData: [22, 23, 25, 28, 30, 29, 26],
                humidityData: [55, 60, 62, 58, 57, 59, 61],
                lightData: [300, 350, 400, 450, 420, 380, 390]
            };

            var ctx = document.getElementById('environmentChart').getContext('2d');
            var environmentChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: sampleData.timestamps, // Nhãn trên trục X (thời gian)
                    datasets: [
                        {
                            label: 'Nhiệt độ (°C)',
                            data: sampleData.temperatureData,
                            borderColor: 'rgba(75, 192, 192, 1)',
                            backgroundColor: 'rgba(75, 192, 192, 0.3)',
                            borderWidth: 3,
                            fill: true,  // Make the area below the line filled with color
                            tension: 0.4,  // Smooth the line
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Độ ẩm (%)',
                            data: sampleData.humidityData,
                            borderColor: 'rgba(54, 162, 235, 1)',
                            backgroundColor: 'rgba(54, 162, 235, 0.3)',
                            borderWidth: 3,
                            fill: true,  // Make the area below the line filled with color
                            tension: 0.4,  // Smooth the line
                            pointRadius: 4,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Ánh sáng (lux)',
                            data: sampleData.lightData,
                            borderColor: 'rgba(255, 206, 86, 1)',
                            backgroundColor: 'rgba(255, 206, 86, 0.3)',
                            borderWidth: 3,
                            fill: true,  // Make the area below the line filled with color
                            tension: 0.4,  // Smooth the line
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: 'Thời gian',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Giá trị',
                                font: {
                                    size: 14,
                                    weight: 'bold'
                                }
                            },
                            beginAtZero: true  // Start the y-axis at zero
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                font: {
                                    size: 14
                                }
                            }
                        },
                        tooltip: {
                            mode: 'index',
                            intersect: false,
                            titleFont: {
                                size: 14,
                                weight: 'bold'
                            },
                            bodyFont: {
                                size: 12
                            }
                        }
                    },
                    animation: {
                        duration: 1000, // 1 second
                        easing: 'easeInOutCubic'
                    }
                }
            });

            // Hàm cập nhật thời gian thực
            function updateClock() {
                var now = new Date();
                var hours = now.getHours().toString().padStart(2, '0');
                var minutes = now.getMinutes().toString().padStart(2, '0');
                var seconds = now.getSeconds().toString().padStart(2, '0');
                var currentTime = `${hours}:${minutes}:${seconds}`;
                $('#realTimeClock').text(currentTime);
            }

            // Cập nhật đồng hồ mỗi giây
            setInterval(updateClock, 1000);

            // Hàm cập nhật màu của các box chứa giá trị môi trường
            function updateBoxColors() {
                // Nhiệt độ
                var temperature = parseInt($('#temperature-value').text());
                if (temperature < 20) {
                    $('#temperature-card').css('background-color', '#85C1E9'); // Xanh da trời
                } else if (temperature >= 20 && temperature < 30) {
                    $('#temperature-card').css('background-color', '#ABEBC6'); // Xanh lá cây
                } else {
                    $('#temperature-card').css('background-color', '#F1948A'); // Đỏ nhạt
                }

                // Độ ẩm
                var humidity = parseInt($('#humidity-value').text());
                if (humidity < 40) {
                    $('#humidity-card').css('background-color', '#AED6F1'); // Xanh nhạt
                } else if (humidity >= 40 && humidity < 70) {
                    $('#humidity-card').css('background-color', '#A9DFBF'); // Xanh lá nhạt
                } else {
                    $('#humidity-card').css('background-color', '#F5B7B1'); // Đỏ nhạt
                }

                // Ánh sáng
                var light = parseInt($('#light-value').text());
                if (light < 300) {
                    $('#light-card').css('background-color', '#F9E79F'); // Vàng nhạt
                } else if (light >= 300 && light < 600) {
                    $('#light-card').css('background-color', '#FAD7A0'); // Cam nhạt
                } else {
                    $('#light-card').css('background-color', '#F5B7B1'); // Đỏ nhạt
                }
            }

            // Cập nhật màu mỗi khi trang được tải
            updateBoxColors();
        });
    </script>
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('.toggle-switch-checkbox').change(function() {
            var checkbox = $(this);
            var deviceId = checkbox.data('id');
            var newStatus = checkbox.is(':checked');

            $.ajax({
                url: '/control/toggle-device',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    device_id: deviceId,
                    status: newStatus
                },
                success: function(response) {
                    if (response.status === 'success') {
                        // Cập nhật biểu tượng dựa trên trạng thái mới
                        var deviceIcon = checkbox.closest('.card-body').find('img');
                        if (response.device.name === 'Quạt') {
                            if (response.device.status) {
                                deviceIcon.addClass('spin');
                            } else {
                                deviceIcon.removeClass('spin');
                            }
                        }
                        
                        // Cập nhật src của biểu tượng dựa trên loại thiết bị
                        if (response.device.name === 'Đèn') {
                            var lightIcon = $('#light');
                            lightIcon.attr('src', response.device.status ? '{{ asset('assets/img/lightbulb-fill.png') }}' : '{{ asset('assets/img/lightbulb.png') }}');
                        }
                        if (response.device.name === 'Điều hòa') {
                            var acIcon = $('#ac');
                            acIcon.attr('src', response.device.status ? '{{ asset('assets/img/ac-on.png') }}' : '{{ asset('assets/img/ac.png') }}');
                        }

                        // Cập nhật bảng lịch sử
                        var row = '<tr><td>' + response.device.name + '</td><td>' + (response.device.status ? 'Bật' : 'Tắt') + '</td><td>' + response.device.last_toggle_at + '</td></tr>';
                        $('#device-history').prepend(row);

                        // Cập nhật văn bản trạng thái
                        var statusText = checkbox.closest('.card').find('.device-status');
                        statusText.text(response.device.status ? 'Thiết bị đang bật' : 'Thiết bị đang tắt');
                    } else {
                        console.log('Failed to toggle device:', response.message);
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error in AJAX request:', error);
                }
            });
        });
    });
</script>


<style>
    .card-header h4 {
        font-size: 1.5rem; /* Tăng kích thước font chữ */
        margin-bottom: 0.5rem;
    }

    .card-header p {
        font-size: 1rem;
        color: #666;
        margin-bottom: 0;
    }

    /* Container for the toggle switch */
    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 60px; /* Width of the switch */
        height: 30px; /* Height of the switch */
    }

    /* Hide the default checkbox */
    .toggle-switch-checkbox {
        display: none;
    }

    /* The slider */
    .toggle-switch-slider {
        position: absolute;
        cursor: pointer;
        inset: 0;
        background-color: #ccc;
        transition: 0.4s;
        border-radius: 30px; /* Rounded corners */
    }

    /* Before the slider */
    .toggle-switch-slider:before {
        content: "";
        position: absolute;
        height: 26px;
        width: 26px;
        left: 2px;
        bottom: 2px;
        background-color: white;
        transition: 0.4s;
        border-radius: 50%;
        box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.2);
    }

    /* Checked state */
    .toggle-switch-checkbox:checked + .toggle-switch-slider {
        background-color: #4caf50; /* Color when toggled on */
    }

    /* Checked state - move slider */
    .toggle-switch-checkbox:checked + .toggle-switch-slider:before {
        transform: translateX(30px); /* Move the slider */
    }

    /* Text inside the switch */
    .toggle-switch-slider:after {
        content: "Tắt";
        position: absolute;
        top: 50%;
        left: 8px;
        transform: translateY(-50%);
        font-size: 12px;
        color: white;
        font-weight: bold;
    }

    .toggle-switch-checkbox:checked + .toggle-switch-slider:after {
        content: "Bật";
        left: auto;
        right: 8px;
    }

    /* Fan spinning effect */
    .device-img {
        width: 100px;
        height: 100px;
        transition: transform 0.5s ease-in-out;
    }

    .device-img.spin {
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }

</style>
<style>
    .table {
        width: 100%; /* Đảm bảo bảng chiếm toàn bộ chiều rộng */
    }
    .table th, .table td {
        text-align: center; /* Căn giữa văn bản trong ô */
        vertical-align: middle; /* Căn giữa theo chiều dọc */
    }
    .table thead th {
        background-color: #f8f9fa; /* Màu nền cho tiêu đề */
    }
    .table tbody tr {
        border-bottom: 1px solid #dee2e6; /* Đường viền dưới mỗi hàng */
    }
</style>
