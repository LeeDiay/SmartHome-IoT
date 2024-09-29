<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Device;

class DashboardController extends Controller
{
    // public function index()
    // {
    //     return view('dashboard.index');
    // }

    public function index()
    {
        $devices = Device::all(); // Lấy tất cả các thiết bị từ database
        return view('dashboard.index', compact('devices'));
    }
}
