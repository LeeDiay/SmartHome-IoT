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
                            <!-- Tùy chọn kích thước trang -->
                            <form action="{{ route('device-history.index') }}" method="GET" class="mb-3 text-end">
                                <label for="page_size" class="form-label font-weight-bold">Số bản ghi</label>
                                <select name="page_size" id="page_size" class="form-select form-select-lg d-inline-block w-auto border " onchange="this.form.submit()">
                                    <option value="10" {{ $pageSize == 10 ? 'selected' : '' }}>10</option>
                                    <option value="20" {{ $pageSize == 20 ? 'selected' : '' }}>20</option>
                                    <option value="30" {{ $pageSize == 30 ? 'selected' : '' }}>30</option>
                                    <option value="50" {{ $pageSize == 50 ? 'selected' : '' }}>50</option>
                                </select>
                            </form>
                        </div>

                        <div class="card-body ">
                            <!-- Form tìm kiếm theo thời gian -->
                            <form action="{{ route('device-history.index') }}" method="GET" class="mb-4">
                                <div class="row align-items-center"> <!-- Căn giữa các phần tử trong hàng -->
                                <label for="search_time" class="form-label">Nhập thời gian (dd-mm-yyyy hoặc hh:MM:ss)</label>
                                    <div class="col-md-3">
                                        <input type="text" name="search_time" id="search_time" class="form-control border border-dark" 
                                            value="{{ $searchTime }}" 
                                            placeholder="  Ví dụ: 2024-09-28 hoặc 09:33:00">
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary" style="height: 100%;">Tìm kiếm</button>
                                    </div>
                                </div>
                            </form>

                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            ID
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            TÊN THIẾT BỊ
                                        </th>
                                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            TRẠNG THÁI
                                        </th>
                                        <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            THỜI GIAN
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                    @foreach($deviceHistory as $index => $toggle)
                                    <tr>
                                        <td>{{ $deviceHistory->firstItem() + $index }}</td>
                                        <td>
                                            <div class="d-flex px-2 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $toggle->device_name }}</h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $toggle->status ? 'Bật' : 'Tắt' }}</h6>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center text-sm">
                                            <p class="text-xs text-secondary mb-0">{{ \Carbon\Carbon::parse($toggle->toggled_at)->format('H:i:s d-m-Y') }}</p>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>


                            <!-- Phân trang -->
                            <div class="d-flex justify-content-center mt-4">
                                {{ $deviceHistory->appends(['page_size' => $pageSize, 'search_time' => $searchTime])->links('pagination::bootstrap-4') }}
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
