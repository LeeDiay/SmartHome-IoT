<x-layout bodyClass="g-sidenav-show bg-gray-200">

    <x-navbars.sidebar activePage="user-profile"></x-navbars.sidebar>
    <div class="main-content position-relative bg-gray-100 max-height-vh-100 h-100">
        <!-- Navbar -->
        <x-navbars.navs.auth titlePage='Trang cá nhân'></x-navbars.navs.auth>
        <!-- End Navbar -->
        <div class="container-fluid px-2 px-md-4">
            <div class="page-header min-height-300 border-radius-xl mt-4"
                style="background-image: url('https://images.unsplash.com/photo-1531512073830-ba890ca4eba2?ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');">
                <span class="mask  bg-gradient-primary  opacity-6"></span>
            </div>
            <div class="card card-body mx-3 mx-md-4 mt-n6">
                <div class="row gx-4 mb-2">
                    <div class="col-auto">
                        <div class="avatar avatar-xl position-relative">
                            <img src="assets/img/avatar_user/{{ auth()->user()->avatar}}" class="w-150 border-radius-lg shadow-sm">
                        </div>
                    </div>
                    <div class="col-auto my-auto">
                        <div class="h-100">
                            <h5 class="mb-1">
                                Lê Đức Anh
                            </h5>
                            <p class="mb-0 font-weight-normal text-sm">
                                B21DCAT026
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Static Section for Report and API Doc Links -->
                <div class="row gx-4 mb-2">
                    <div class="mb-3 col-md-4">
                        <a href="https://drive.google.com/file/d/14xE_URUkanJiBqTBKtDRhIoWwqPYmIME/view?usp=drive_link" class="form-control border border-2 p-2" target="_blank">
                            Link Báo cáo PDF
                        </a>
                    </div>
                    <div class="mb-3 col-md-4">
                        <a href="http://160.30.54.8/swagger-ui/dist/" class="form-control border border-2 p-2" target="_blank">
                            API Documentation
                        </a>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label class="form-label">Link Git</label>
                        <a href="https://github.com/LeeDiay/SmartHome-IoT" class="form-control border border-2 p-2" target="_blank">
                            Link Git
                        </a>
                    </div>
                </div>

                <!-- Other Information Display -->
                <div class="card card-plain h-100">
                    <div class="card-header pb-0 p-3">
                        <h6 class="mb-3">Thông tin cá nhân</h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="row">
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Mã sinh viên</label>
                                <input class="form-control border border-2 p-2" value='B21DCAT026' disabled>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Email</label>
                                <input class="form-control border border-2 p-2" value='{{ auth()->user()->email }}' disabled>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Họ và tên</label>
                                <input class="form-control border border-2 p-2" value='{{ auth()->user()->name }}' disabled>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Số điện thoại</label>
                                <input class="form-control border border-2 p-2" value='{{ auth()->user()->phone }}' disabled>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nơi ở</label>
                                <input class="form-control border border-2 p-2" value='{{ auth()->user()->location }}' disabled>
                            </div>
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Github</label>
                                <input class="form-control border border-2 p-2" value='https://github.com/LeeDiay' disabled>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <x-footers.auth></x-footers.auth>
    </div>
    <x-plugins></x-plugins>

</x-layout>
