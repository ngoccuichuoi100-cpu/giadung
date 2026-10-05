<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PointTransaction;
use App\Models\Voucher;
use App\Models\UserVoucher;
use App\Http\Controllers\CartController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Requests\RegisterStep1Request;
use App\Http\Requests\RegisterStep2Request;
use App\Mail\WelcomeNewUserMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showRegister()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.register');
    }

    public function register(RegisterStep1Request $request)
    {
        $email = $request->email;
        if (empty($email) && $request->filled('username')) {
            $email = $request->username . '_' . time() . '@temp.giadung.com';
        }

        $username = $request->username;
        if (empty($username) && !empty($email)) {
            $raw = strtolower(explode('@', $email)[0]);
            $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $raw) ?: 'user';
            $candidate = $cleaned;
            $i = 1;
            while (User::where('username', $candidate)->exists()) {
                $candidate = $cleaned . $i++;
            }
            $username = $candidate;
        }

        $user = User::create([
            'username' => $username,
            'name' => $username,
            'email' => $email,
            'password' => Hash::make($request->password),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->assignNewMemberVouchers($user);

        Auth::login($user);

        CartController::mergeSessionCartToDb($user);

        session()->flash('just_registered', true);

        // Gửi email báo "Bạn đã tạo tài khoản thành công" cho KH
        if (!empty($user->email) && !str_ends_with($user->email, '@temp.giadung.com')) {
            try {
                Mail::to($user->email)->send(new WelcomeNewUserMail($user));
            } catch (\Throwable $e) {
                Log::error('Lỗi gửi email đăng ký cho user ID ' . $user->id . ': ' . $e->getMessage());
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đăng ký tài khoản thành công!',
                'redirect' => route('register.welcome'),
                'username' => $user->username,
                'csrf_token' => csrf_token()
            ]);
        }

        return redirect()->route('register.welcome')->with('success', 'Đăng ký tài khoản thành công!');
    }

    public function updateProfile(RegisterStep2Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đăng nhập trước.'
            ], 401);
        }

        // Validate friend invite code if entered
        $referrer = null;
        if ($request->filled('friend_invite_code')) {
            $referrer = User::where('referral_code', $request->friend_invite_code)->first();
            if (!$referrer) {
                return response()->json([
                    'errors' => [
                        'friend_invite_code' => ['Mã mời không tồn tại trong hệ thống.']
                    ]
                ], 422);
            }
            if ($referrer->id === $user->id) {
                return response()->json([
                    'errors' => [
                        'friend_invite_code' => ['Bạn không thể sử dụng mã mời của chính mình.']
                    ]
                ], 422);
            }
        }

        // Update profile
        $user->name = $request->name;
        $user->age = $request->age;
        $user->gender = $request->gender;
        $user->address = $request->address;
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->phone = $request->phone;

        // Apply referral reward if invite code entered
        if ($referrer && empty($user->friend_invite_code)) {
            $user->friend_invite_code = $request->friend_invite_code;
            
            // Reward referrer: 100 points
            $referrer->points += 100;
            $referrer->save();
            PointTransaction::create([
                'user_id' => $referrer->id,
                'amount' => 100,
                'type' => 'earn',
                'description' => 'Thưởng giới thiệu thành viên mới: ' . $user->username,
            ]);

            // Reward user: 50 points
            $user->points += 50;
            PointTransaction::create([
                'user_id' => $user->id,
                'amount' => 50,
                'type' => 'earn',
                'description' => 'Thưởng nhập mã mời từ bạn bè: ' . $referrer->username,
            ]);
        }

        $user->save();

        session()->flash('just_registered', true);

        // Gửi email báo "Bạn đã tạo tài khoản thành công" cho khách hàng
        if (!empty($user->email) && !str_ends_with($user->email, '@temp.giadung.com')) {
            try {
                Mail::to($user->email)->send(new WelcomeNewUserMail($user));
            } catch (\Throwable $e) {
                Log::error('Lỗi gửi email đăng ký thành công cho user ID ' . $user->id . ': ' . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công!',
            'username' => $user->username
        ]);
    }

    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ], [
            'login.required' => 'Tên tài khoản hoặc Email là bắt buộc.',
            'password.required' => 'Mật khẩu là bắt buộc.',
        ]);

        $loginInput = $request->login;
        $user = User::where('email', $loginInput)->orWhere('username', $loginInput)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if (!$user->isActive()) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return back()->withErrors([
                    'login' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ]);
            }

            Auth::login($user, $request->boolean('remember'));
            $request->session()->regenerate();

            CartController::mergeSessionCartToDb($user);

            if ($user->hasAdminAccess()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Đăng nhập thành công!');
        }

        return back()->withErrors([
            'login' => 'Thông tin đăng nhập không chính xác.',
        ])->onlyInput('login');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Đăng xuất thành công.');
    }

    public function showWelcome()
    {
        $user = Auth::user();
        if (!$user || (!session()->has('just_registered') && !session()->has('welcome_viewed'))) {
            return redirect()->route('home');
        }
        
        session()->keep('just_registered');
        session()->put('welcome_viewed', true);

        return view('auth.welcome', compact('user'));
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();
        } catch (\Throwable $e) {
            Log::error('Google OAuth error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors([
                'login' => 'Không thể kết nối với tài khoản Google. Vui lòng thử lại.',
            ]);
        }

        $email = $googleUser->getEmail();
        if (empty($email)) {
            return redirect()->route('login')->withErrors([
                'login' => 'Không thể lấy thông tin email từ tài khoản Google của bạn.',
            ]);
        }

        // Tìm tài khoản theo email hoặc google_id
        $user = User::where('email', $email)
            ->orWhere('google_id', $googleUser->getId())
            ->first();

        if ($user) {
            if (!$user->isActive()) {
                return redirect()->route('login')->withErrors([
                    'login' => 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ quản trị viên.',
                ]);
            }

            // Đồng bộ google_id và ảnh đại diện nếu có
            $needsSave = false;
            if (empty($user->google_id)) {
                $user->google_id = $googleUser->getId();
                $needsSave = true;
            }
            if (empty($user->avatar) && $googleUser->getAvatar()) {
                $user->avatar = $googleUser->getAvatar();
                $needsSave = true;
            }
            if ($needsSave) {
                $user->save();
            }

            Auth::login($user, true);
            request()->session()->regenerate();
            CartController::mergeSessionCartToDb($user);

            if ($user->hasAdminAccess()) {
                return redirect()->intended(route('admin.dashboard'));
            }

            return redirect()->intended(route('home'))->with('success', 'Đăng nhập bằng tài khoản Google thành công!');
        }

        // Nếu email này chưa từng đăng ký, tự động khởi tạo tài khoản mới
        $raw = strtolower(explode('@', $email)[0]);
        $cleaned = preg_replace('/[^a-zA-Z0-9]/', '', $raw) ?: 'user';
        $candidate = $cleaned;
        $i = 1;
        while (User::where('username', $candidate)->exists()) {
            $candidate = $cleaned . $i++;
        }

        $newUser = User::create([
            'username' => $candidate,
            'name' => $googleUser->getName() ?: $candidate,
            'email' => $email,
            'password' => Hash::make(bin2hex(random_bytes(16))),
            'google_id' => $googleUser->getId(),
            'avatar' => $googleUser->getAvatar(),
            'role' => 'customer',
            'status' => 'active',
        ]);

        $this->assignNewMemberVouchers($newUser);

        Auth::login($newUser, true);
        request()->session()->regenerate();
        CartController::mergeSessionCartToDb($newUser);
        session()->flash('just_registered', true);

        // Gửi email chào mừng thành viên mới
        try {
            Mail::to($newUser->email)->send(new WelcomeNewUserMail($newUser));
        } catch (\Throwable $e) {
            Log::error('Lỗi gửi email đăng ký qua Google cho user ' . $newUser->id . ': ' . $e->getMessage());
        }

        return redirect()->route('register.welcome');
    }

    /**
     * Tự động gán các voucher dành cho thành viên mới (acquisition_type = 'new_member')
     */
    protected function assignNewMemberVouchers(User $user): void
    {
        try {
            $vouchers = Voucher::where('acquisition_type', 'new_member')
                ->where('status', 'active')
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->get();

            foreach ($vouchers as $voucher) {
                UserVoucher::firstOrCreate([
                    'user_id' => $user->id,
                    'voucher_id' => $voucher->id,
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning("Tặng voucher thành viên mới thất bại cho user {$user->id}: " . $e->getMessage());
        }
    }
}
