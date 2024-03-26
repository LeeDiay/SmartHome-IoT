<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Auth\AuthManager;

class ProfileController extends Controller
{
    public function create()
    {
        return view('pages.profile');
    }


    public function update(Request $request){
        $user = $request->user();
        $attributes = $request->validate([
            'email' => 'required|email|unique:users,email,' . $user->id,
            'name' => 'required|max:30',
            'phone' => 'required|max:10',
            'about' => 'max:150',
            'location' => 'max:300',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            $avatar = $request->file('avatar');
            
            // Kiểm tra xem tệp có phải là hình ảnh hay không
            if ($avatar->isValid() && in_array($avatar->getClientOriginalExtension(), ['jpg', 'jpeg', 'png'])) {
                if (auth()->user()->avatar!='default-avatar.jpg') {
                    // Nếu có, xóa avatar cũ
                    $oldAvatarPath = public_path('assets/img/avatar_user/') . auth()->user()->avatar;
                    if (file_exists($oldAvatarPath)) {
                        unlink($oldAvatarPath); // Xóa tệp tin avatar cũ
                    }
                }
                $avatarName = uniqid('avatar_') . '.' . $avatar->getClientOriginalExtension();
                $avatar->move(public_path('assets/img/avatar_user'), $avatarName);
                $attributes['avatar'] =  $avatarName;
            } else {
                return back()->withErrors(['avatar' => 'Ảnh tải lên không hợp lệ, phải có định dạng: jpg, jpeg, png.']);
            }
        }

        $user->update($attributes);
        return back()->withStatus('Cập nhật thông tin thành công');
    }


    public function index()
    {
        $users = User::all();
        return view('pages.laravel-examples.user-management', ['users' => $users]);
    }

    public function edit(Request $request)
    {
        $user = User::find($request->username);

        $attributes = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|max:30',
            'phone' => 'required|max:10',
            'about' => 'max:150',
            'location' => 'max:300',
            'avatar' => 'sometimes|image|mimes:jpeg,png,jpg|max:2048'
        ]);
        // Lưu thông tin đã cập nhật vào cơ sở dữ liệu
        $user->update($attributes);
        return back()->withStatus('Cập nhật thông tin thành công');
    }
}
