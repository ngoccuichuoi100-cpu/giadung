<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesSchedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SalesScheduleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(function ($request, $next) {
                if (!auth()->check() || (!auth()->user()->isAdmin() && !auth()->user()->isSales())) {
                    abort(403, 'Chỉ Quản trị viên hoặc Nhân viên bán hàng mới có quyền truy cập lịch trực.');
                }
                return $next($request);
            }),
        ];
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $salesStaff = User::where('role', 'sales')->get();

        $query = SalesSchedule::with(['user', 'creator']);

        // If logged in user is a Sales staff (and not Admin), only show their own schedules
        if ($user->isSales() && !$user->isAdmin()) {
            $query->where('user_id', $user->id);
        } elseif ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('date')) {
            $query->whereDate('work_date', $request->date);
        }

        if ($request->filled('month')) {
            $query->whereYear('work_date', date('Y', strtotime($request->month)))
                  ->whereMonth('work_date', date('m', strtotime($request->month)));
        }

        if ($request->filled('q')) {
            $q = trim($request->q);
            if (mb_strlen($q, 'UTF-8') === 1) {
                // Gõ 1 ký tự: Chỉ tìm các từ bắt đầu bằng ký tự này
                $query->where(function ($sub) use ($q) {
                    $sub->where('note', 'like', "{$q}%")
                        ->orWhere('note', 'like', "% {$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "{$q}%")
                              ->orWhere('name', 'like', "% {$q}%")
                              ->orWhere('email', 'like', "{$q}%")
                              ->orWhere('username', 'like', "{$q}%");
                        });
                });
            } else {
                // Gõ >= 2 ký tự: Tìm linh hoạt ở mọi vị trí
                $query->where(function ($sub) use ($q) {
                    $sub->where('note', 'like', "%{$q}%")
                        ->orWhereHas('user', function ($u) use ($q) {
                            $u->where('name', 'like', "%{$q}%")
                              ->orWhere('email', 'like', "%{$q}%")
                              ->orWhere('username', 'like', "%{$q}%");
                        });
                });
            }
        }

        $schedules = $query->orderBy('work_date', 'desc')->paginate(20)->withQueryString();

        return view('admin.schedules.index', compact('schedules', 'salesStaff'));
    }

    public function store(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Chỉ Quản trị viên mới có quyền phân công lịch bán hàng.');
        }

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'work_date' => 'required|date',
            'shift_name' => 'required|string',
            'notes' => 'nullable|string',
        ], [
            'user_id.required' => 'Vui lòng chọn nhân viên bán hàng.',
            'work_date.required' => 'Vui lòng chọn ngày trực bán hàng.',
            'shift_name.required' => 'Vui lòng chọn ca trực.',
        ]);

        SalesSchedule::create([
            'user_id' => $request->user_id,
            'work_date' => $request->work_date,
            'shift_name' => $request->shift_name,
            'notes' => $request->notes,
            'status' => 'assigned',
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('admin.sales-schedules.index')->with('success', 'Phân công ngày trực bán hàng thành công.');
    }

    public function update(Request $request, $id)
    {
        $schedule = SalesSchedule::findOrFail($id);

        if (!auth()->user()->isAdmin() && auth()->id() !== $schedule->user_id) {
            return back()->with('error', 'Bạn không có quyền cập nhật ca làm việc này.');
        }

        $request->validate([
            'status' => 'required|in:assigned,completed,cancelled',
            'notes' => 'nullable|string',
        ]);

        // Sales staff cannot cancel schedule (only admin can cancel)
        if (!auth()->user()->isAdmin() && $request->status === 'cancelled') {
            return back()->with('error', 'Chỉ Quản trị viên mới có quyền hủy ca trực.');
        }

        $schedule->status = $request->status;
        if ($request->has('notes')) {
            $schedule->notes = $request->notes;
        }

        if (auth()->user()->isAdmin() && $request->filled('shift_name')) {
            $schedule->shift_name = $request->shift_name;
        }

        $schedule->save();

        return redirect()->route('admin.sales-schedules.index')->with('success', 'Cập nhật lịch trực bán hàng thành công.');
    }

    public function destroy($id)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Chỉ Quản trị viên mới có quyền xóa lịch bán hàng.');
        }

        $schedule = SalesSchedule::findOrFail($id);
        $schedule->delete();

        return redirect()->route('admin.sales-schedules.index')->with('success', 'Xóa phân công ngày trực thành công.');
    }
}