@extends('layouts.client')

@section('title', 'Hệ thống Bạn Bè - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="text-left mb-8">
            <h1 class="text-3xl font-extrabold text-slate-900 dark:text-white">Hệ thống Bạn Bè</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kết nối với những người mua sắm khác để chia sẻ kinh nghiệm sử dụng sản phẩm</p>
        </div>

        <!-- Preferences panel -->
        <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm mb-8 text-left">
            <h3 class="font-bold text-sm text-slate-800 dark:text-slate-200 mb-2">Cấu hình hệ thống</h3>
            <form action="{{ route('settings.preferences') }}" method="POST" class="flex flex-wrap items-center gap-6 text-xs text-slate-650 dark:text-slate-400">
                @csrf
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="friend_system_enabled" value="1" {{ auth()->user()->settings && auth()->user()->settings->friend_system_enabled ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded border-slate-350">
                    Bật chức năng Bạn bè (cho phép gửi/nhận lời mời)
                </label>
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox" name="notifications_enabled" value="1" {{ auth()->user()->settings && auth()->user()->settings->notifications_enabled ? 'checked' : '' }} class="w-4 h-4 text-orange-500 rounded border-slate-350">
                    Nhận thông báo hệ thống
                </label>
                <button type="submit" class="px-4 py-1.5 bg-slate-900 dark:bg-slate-700 hover:bg-orange-500 text-white rounded-xl font-semibold shadow">Lưu thiết lập</button>
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Left Column: Friends List -->
            <div class="lg:col-span-2 space-y-6 text-left">
                
                <!-- Active Friends -->
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b pb-2 dark:border-slate-700">Danh sách bạn bè ({{ $friends->count() }})</h2>
                    <div class="space-y-4">
                        @forelse($friends as $friend)
                            <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/40 rounded-2xl hover:bg-orange-50/20 dark:hover:bg-slate-700/30 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 dark:bg-orange-950/60 text-orange-600 dark:text-orange-400 font-bold flex items-center justify-center">
                                        {{ substr($friend->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs block text-slate-800 dark:text-slate-200">{{ $friend->name }}</span>
                                        <span class="text-[10px] text-slate-400 block">{{ $friend->email }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <form action="{{ route('friends.remove', $friend->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1.5 border border-red-200 text-red-500 hover:bg-red-50 dark:hover:bg-red-950/20 text-[10px] font-bold rounded-xl">Hủy kết bạn</button>
                                    </form>
                                    <form action="{{ route('friends.block') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="user_id" value="{{ $friend->id }}">
                                        <button type="submit" class="px-3 py-1.5 bg-slate-100 dark:bg-slate-700 text-slate-500 hover:text-red-500 dark:text-slate-300 text-[10px] font-bold rounded-xl">Chặn</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center text-slate-400 text-xs">Bạn chưa kết bạn với ai. Hãy gửi lời mời tới người dùng khác!</div>
                        @endforelse
                    </div>
                </div>

                <!-- Blocked list -->
                @if($blockedUsers->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white mb-4 border-b pb-2 dark:border-slate-700">Đang chặn ({{ $blockedUsers->count() }})</h2>
                        <div class="space-y-3">
                            @foreach($blockedUsers as $blocked)
                                <div class="flex items-center justify-between p-3 bg-red-50/20 dark:bg-red-950/10 rounded-2xl">
                                    <div class="flex items-center gap-3">
                                        <span class="text-slate-400">🚫</span>
                                        <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $blocked->name }}</span>
                                    </div>
                                    <form action="{{ route('friends.unblock', $blocked->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-slate-900 dark:bg-slate-700 text-white hover:bg-orange-500 text-[10px] font-bold rounded-xl">Bỏ chặn</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right Column: Requests & Finding -->
            <div class="space-y-6 text-left">
                
                <!-- Pending Requests -->
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-4 border-b pb-2 dark:border-slate-700">Lời mời kết bạn nhận được</h3>
                    <div class="space-y-3">
                        @forelse($pendingRequests as $req)
                            <div class="p-3 bg-slate-50 dark:bg-slate-900/40 rounded-2xl space-y-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-100 dark:bg-orange-950 text-orange-600 dark:text-orange-400 font-bold flex items-center justify-center">
                                        {{ substr($req->sender->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-xs block text-slate-800 dark:text-slate-200">{{ $req->sender->name }}</span>
                                        <span class="text-[9px] text-slate-400 block">{{ $req->created_at->diffForHumans() }}</span>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <form action="{{ route('friends.accept', $req->user_id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-bold rounded-xl shadow shadow-orange-500/10">Đồng ý</button>
                                    </form>
                                    <form action="{{ route('friends.decline', $req->user_id) }}" method="POST" class="flex-1">
                                        @csrf
                                        <button type="submit" class="w-full py-1.5 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 text-slate-700 dark:text-slate-200 text-[10px] font-bold rounded-xl">Từ chối</button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="py-6 text-center text-slate-400 text-xs">Không có lời mời nào.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Sent Requests -->
                @if($sentRequests->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                        <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-4 border-b pb-2 dark:border-slate-700">Đã gửi lời mời</h3>
                        <div class="space-y-3">
                            @foreach($sentRequests as $sReq)
                                <div class="flex items-center justify-between p-3 bg-slate-50 dark:bg-slate-900/30 rounded-2xl">
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs font-bold">{{ $sReq->recipient->name }}</span>
                                    </div>
                                    <form action="{{ route('friends.remove', $sReq->friend_id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="text-[10px] text-slate-400 hover:text-red-500">Hủy</button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Find Friends -->
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-3xl p-6 shadow-sm">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white mb-4 border-b pb-2 dark:border-slate-700/70">Gợi ý kết bạn</h3>
                    <div class="space-y-4">
                        @forelse($potentialFriends as $pFriend)
                            <div class="flex items-center justify-between p-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-350 font-bold flex items-center justify-center">
                                        {{ substr($pFriend->name, 0, 1) }}
                                    </div>
                                    <span class="font-bold text-xs text-slate-800 dark:text-slate-200">{{ $pFriend->name }}</span>
                                </div>
                                <form action="{{ route('friends.send') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="friend_id" value="{{ $pFriend->id }}">
                                    <button type="submit" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white text-[10px] font-bold rounded-xl shadow shadow-orange-500/10">Kết bạn</button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-slate-400 text-xs py-4">Không tìm thấy người dùng phù hợp.</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
