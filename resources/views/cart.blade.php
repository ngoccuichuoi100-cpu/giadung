@extends('layouts.client')

@section('title', 'Giỏ hàng của tôi - GiaDụngPro')

@section('content')
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <h1 class="text-3xl font-extrabold text-slate-900 mb-8">Giỏ hàng của bạn</h1>

        @if($cartItems->isEmpty())
            <div class="bg-white border border-slate-100 rounded-3xl p-12 text-center shadow-sm">
                <svg class="w-20 h-20 text-slate-200 mx-auto mb-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <h2 class="font-bold text-slate-800 text-xl mb-2">Giỏ hàng của bạn trống</h2>
                <p class="text-sm text-slate-500 mb-6 max-w-sm mx-auto">Vui lòng quay lại cửa hàng để lựa chọn các sản phẩm gia dụng chất lượng cao cho gia đình mình nhé!</p>
                <a href="{{ route('products.index') }}" class="px-6 py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white font-bold rounded-full shadow-lg transition-all duration-300">
                    Khám phá sản phẩm
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Cart Items List -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white border border-slate-100 rounded-3xl overflow-hidden shadow-sm">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-55 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase">
                                    <th class="p-6">Sản phẩm</th>
                                    <th class="p-6 text-center">Số lượng</th>
                                    <th class="p-6 text-right">Tổng tiền</th>
                                    <th class="p-6 text-center">Xóa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($cartItems as $item)
                                    <tr>
                                        <!-- Product Detail Info -->
                                        <td class="p-6">
                                            <div class="flex items-center gap-4">
                                                <div class="w-16 h-16 bg-slate-50 border border-slate-200 rounded-xl overflow-hidden flex-shrink-0">
                                                    @if($item->product->thumbnail)
                                                        <img src="{{ $item->product->thumbnail_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                                    @else
                                                        <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div>
                                                    <h3 class="font-bold text-slate-800 text-sm hover:text-orange-500 transition-colors line-clamp-1">
                                                        <a href="{{ route('products.show', $item->product->slug) }}">{{ $item->product->name }}</a>
                                                    </h3>
                                                    <span class="text-xs text-slate-400 font-semibold uppercase block mt-0.5">{{ $item->product->brand ?? 'GiaDụngPro' }}</span>
                                                    @if($item->variant)
                                                        <span class="text-xs text-slate-500 block mt-0.5">
                                                            @if($item->color) Màu: {{ $item->color->name }} @endif
                                                            @if($item->size) | Size: {{ $item->size->name }} @endif
                                                        </span>
                                                    @endif
                                                    @if($item->warranty)
                                                        <span class="text-xs text-emerald-600 font-medium block mt-0.5">
                                                            Bảo hành: {{ $item->warranty->name }} (+{{ number_format($item->warranty->price, 0, ',', '.') }} ₫)
                                                        </span>
                                                    @endif
                                                    <span class="text-xs text-orange-500 font-bold block mt-1">{{ number_format($item->unit_price, 0, ',', '.') }} ₫</span>
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Update Quantity Form -->
                                        <td class="p-6">
                                            <form action="{{ route('cart.update') }}" method="POST" class="flex items-center justify-center gap-1.5">
                                                @csrf
                                                <input type="hidden" name="cart_item_id" value="{{ $item->id }}">
                                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->variant ? $item->variant->stock_quantity : $item->product->stock_quantity }}"
                                                    class="w-16 text-center px-2 py-1 bg-slate-50 border border-slate-200 rounded-lg text-sm font-semibold focus:outline-none focus:ring-1 focus:ring-orange-500 focus:bg-white">
                                                <button type="submit" class="p-1.5 bg-slate-900 hover:bg-orange-500 text-white rounded-lg transition-all text-xs" title="Cập nhật">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 1121.21 8H18.2M7 9a4 4 0 118 0v0a4 4 0 01-8 0z"></path></svg>
                                                </button>
                                            </form>
                                        </td>

                                        <!-- Subtotal per item -->
                                        <td class="p-6 text-right font-bold text-slate-800 text-sm">
                                            {{ number_format($item->line_price, 0, ',', '.') }} ₫
                                        </td>

                                        <!-- Remove Item Action -->
                                        <td class="p-6 text-center">
                                            <form action="{{ route('cart.remove', $item->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?');">
                                                @csrf
                                                <button type="submit" class="text-slate-400 hover:text-red-500 transition-colors">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Cart Helpers Actions -->
                    <div class="flex justify-between items-center">
                        <a href="{{ route('products.index') }}" class="text-sm font-semibold text-orange-500 hover:text-orange-600 flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                            Tiếp tục mua hàng
                        </a>

                        <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa toàn bộ sản phẩm khỏi giỏ hàng?');">
                            @csrf
                            <button type="submit" class="text-sm font-semibold text-slate-500 hover:text-red-500 transition-colors">
                                Xóa sạch giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Order Pricing Card Summary -->
                <div class="bg-white border border-slate-100 rounded-3xl p-6 shadow-sm h-fit space-y-6">
                    <h3 class="font-bold text-slate-800 text-lg border-b border-slate-100 pb-3">Tóm tắt đơn hàng</h3>
                    
                    <div class="space-y-3.5 text-sm text-slate-600">
                        <div class="flex justify-between">
                            <span>Tạm tính</span>
                            <span class="font-bold text-slate-800">{{ number_format($subtotal, 0, ',', '.') }} ₫</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-bold text-slate-800">
                                @if($shippingFee === 0)
                                    Miễn phí
                                @else
                                    {{ number_format($shippingFee, 0, ',', '.') }} ₫
                                @endif
                            </span>
                        </div>
                        @if($shippingFee > 0)
                            <p class="text-xs text-orange-500 italic mt-0.5">Miễn phí vận chuyển cho đơn hàng từ 500.000 ₫ trở lên.</p>
                        @endif
                    </div>

                    <div class="border-t border-slate-100 pt-4 flex justify-between text-base font-extrabold text-slate-900">
                        <span>Tổng tiền</span>
                        <span class="text-xl text-orange-500">{{ number_format($totalAmount, 0, ',', '.') }} ₫</span>
                    </div>

                    <a href="{{ route('checkout.index') }}" class="block w-full py-3 bg-gradient-to-r from-orange-500 to-amber-500 hover:from-orange-600 hover:to-amber-600 text-white text-center font-bold rounded-2xl shadow-md hover:shadow-lg transition-all duration-300">
                        Tiến Hành Đặt Hàng
                    </a>
                </div>
            </div>
        @endif
    </div>
@endsection
