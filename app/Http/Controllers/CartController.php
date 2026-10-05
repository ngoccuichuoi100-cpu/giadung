<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Color;
use App\Models\Size;
use App\Models\Warranty;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = collect();
        $subtotal = 0;

        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $cartItems = $cart->items()->with(['product', 'variant', 'color', 'size', 'warranty'])->get();
        } else {
            // Load from session
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $index => $item) {
                $product = Product::find($item['product_id']);
                if (!$product) continue;

                $variant = !empty($item['product_variant_id']) ? ProductVariant::find($item['product_variant_id']) : null;
                $color = !empty($item['color_id']) ? Color::find($item['color_id']) : null;
                $size = !empty($item['size_id']) ? Size::find($item['size_id']) : null;
                $warranty = !empty($item['warranty_id']) ? Warranty::find($item['warranty_id']) : null;

                $mockItem = new CartItem();
                $mockItem->id = $index; // Temporary ID matching index
                $mockItem->product_id = $product->id;
                $mockItem->product_variant_id = $item['product_variant_id'];
                $mockItem->color_id = $item['color_id'];
                $mockItem->size_id = $item['size_id'];
                $mockItem->warranty_id = $item['warranty_id'];
                $mockItem->quantity = $item['quantity'];

                $mockItem->setRelation('product', $product);
                if ($variant) $mockItem->setRelation('variant', $variant);
                if ($color) $mockItem->setRelation('color', $color);
                if ($size) $mockItem->setRelation('size', $size);
                if ($warranty) $mockItem->setRelation('warranty', $warranty);

                $cartItems->push($mockItem);
            }
        }

        foreach ($cartItems as $item) {
            $price = $item->variant ? $item->variant->active_price : $item->product->active_price;
            
            // Add warranty price if any
            if ($item->warranty) {
                $price += $item->warranty->price;
            }

            $subtotal += $price * $item->quantity;
        }

        $shippingFee = $subtotal >= 500000 ? 0 : 30000;
        if ($cartItems->isEmpty()) {
            $shippingFee = 0;
        }
        $totalAmount = $subtotal + $shippingFee;

        return view('cart', compact('cartItems', 'subtotal', 'shippingFee', 'totalAmount'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'color_id' => 'nullable|exists:colors,id',
            'size_id' => 'nullable|exists:sizes,id',
            'warranty_id' => 'nullable|exists:warranties,id',
            'quantity' => 'required|integer|min:1',
        ]);

        $product = Product::findOrFail($request->product_id);

        if ($product->status !== 'active') {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm này đã ngừng hoạt động.'], 422);
            }
            return back()->with('error', 'Sản phẩm này đã ngừng hoạt động.');
        }

        // Validate variants
        $variantsCount = ProductVariant::where('product_id', $product->id)->where('status', 'active')->count();
        if ($variantsCount > 0 && empty($request->product_variant_id)) {
            $defaultVariant = ProductVariant::where('product_id', $product->id)->where('status', 'active')->first();
            if ($defaultVariant) {
                $request->merge([
                    'product_variant_id' => $defaultVariant->id,
                    'color_id' => $defaultVariant->color_id,
                    'size_id' => $defaultVariant->size_id,
                ]);
            } else {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json([
                        'success' => false, 
                        'message' => 'Vui lòng xem chi tiết sản phẩm để chọn phân loại hàng.',
                        'redirect' => route('products.show', $product->slug)
                    ], 422);
                }
                return redirect()->route('products.show', $product->slug)->with('error', 'Vui lòng chọn đầy đủ màu sắc và kích thước cho sản phẩm.');
            }
        }

        $variant = null;
        if ($request->product_variant_id) {
            $variant = ProductVariant::findOrFail($request->product_variant_id);
            if ($variant->status !== 'active') {
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'message' => 'Biến thể sản phẩm này hiện không khả dụng.'], 422);
                }
                return back()->with('error', 'Biến thể sản phẩm này hiện không khả dụng.');
            }
        }

        // Check stock
        $maxStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
        
        // Find current quantity in cart
        $currentQty = 0;
        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $request->product_variant_id)
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->where('warranty_id', $request->warranty_id)
                ->first();
            $currentQty = $cartItem ? $cartItem->quantity : 0;
        } else {
            $sessionCart = session('cart', []);
            foreach ($sessionCart as $item) {
                if ($item['product_id'] == $product->id &&
                    $item['product_variant_id'] == $request->product_variant_id &&
                    $item['color_id'] == $request->color_id &&
                    $item['size_id'] == $request->size_id &&
                    $item['warranty_id'] == $request->warranty_id) {
                    $currentQty = $item['quantity'];
                    break;
                }
            }
        }

        $newQty = $currentQty + $request->quantity;
        if ($newQty > $maxStock) {
            $msg = "Không thể thêm. Tổng số lượng muốn mua ({$newQty}) vượt quá tồn kho (Còn lại: {$maxStock}).";
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return back()->with('error', $msg);
        }

        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::firstOrCreate(['user_id' => $user->id]);
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $product->id)
                ->where('product_variant_id', $request->product_variant_id)
                ->where('color_id', $request->color_id)
                ->where('size_id', $request->size_id)
                ->where('warranty_id', $request->warranty_id)
                ->first();

            if ($cartItem) {
                $cartItem->update(['quantity' => $newQty]);
            } else {
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $product->id,
                    'product_variant_id' => $request->product_variant_id,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id,
                    'warranty_id' => $request->warranty_id,
                    'quantity' => $request->quantity,
                ]);
            }
        } else {
            // Save to session
            $sessionCart = session('cart', []);
            $found = false;
            foreach ($sessionCart as $key => $item) {
                if ($item['product_id'] == $product->id &&
                    $item['product_variant_id'] == $request->product_variant_id &&
                    $item['color_id'] == $request->color_id &&
                    $item['size_id'] == $request->size_id &&
                    $item['warranty_id'] == $request->warranty_id) {
                    $sessionCart[$key]['quantity'] = $newQty;
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                $sessionCart[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $request->product_variant_id,
                    'color_id' => $request->color_id,
                    'size_id' => $request->size_id,
                    'warranty_id' => $request->warranty_id,
                    'quantity' => $request->quantity,
                ];
            }

            session(['cart' => $sessionCart]);
        }

        // Calculate updated cart items count
        $totalCount = Auth::check() && Auth::user()->cart 
            ? Auth::user()->cart->items()->sum('quantity') 
            : array_sum(array_column(session('cart', []), 'quantity'));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã thêm sản phẩm vào giỏ hàng thành công!',
                'cart_count' => $totalCount,
            ]);
        }

        if ($request->input('action') === 'buy_now') {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'cart_item_id' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        $quantity = $request->quantity;

        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('id', $request->cart_item_id)
                ->firstOrFail();

            $maxStock = $cartItem->variant ? $cartItem->variant->stock_quantity : $cartItem->product->stock_quantity;
            if ($quantity > $maxStock) {
                return back()->with('error', "Số lượng cập nhật ({$quantity}) vượt quá tồn kho (Còn lại: {$maxStock}).");
            }

            $cartItem->update(['quantity' => $quantity]);
        } else {
            $sessionCart = session('cart', []);
            $index = $request->cart_item_id;

            if (isset($sessionCart[$index])) {
                $item = $sessionCart[$index];
                $product = Product::findOrFail($item['product_id']);
                $variant = $item['product_variant_id'] ? ProductVariant::find($item['product_variant_id']) : null;
                
                $maxStock = $variant ? $variant->stock_quantity : $product->stock_quantity;
                if ($quantity > $maxStock) {
                    return back()->with('error', "Số lượng cập nhật ({$quantity}) vượt quá tồn kho (Còn lại: {$maxStock}).");
                }

                $sessionCart[$index]['quantity'] = $quantity;
                session(['cart' => $sessionCart]);
            }
        }

        return back()->with('success', 'Cập nhật số lượng thành công.');
    }

    public function remove($id)
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->firstOrFail();
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('id', $id)
                ->firstOrFail();

            $cartItem->delete();
        } else {
            $sessionCart = session('cart', []);
            if (isset($sessionCart[$id])) {
                unset($sessionCart[$id]);
                // Re-index array
                session(['cart' => array_values($sessionCart)]);
            }
        }

        return back()->with('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }

    public function clear()
    {
        if (Auth::check()) {
            $user = Auth::user();
            $cart = Cart::where('user_id', $user->id)->first();
            if ($cart) {
                $cart->items()->delete();
            }
        } else {
            session()->forget('cart');
        }

        return back()->with('success', 'Đã xóa toàn bộ giỏ hàng.');
    }

    /**
     * Merge session cart to user database cart after login.
     */
    public static function mergeSessionCartToDb($user)
    {
        $sessionCart = session('cart', []);
        if (empty($sessionCart)) {
            return;
        }

        $cart = Cart::firstOrCreate(['user_id' => $user->id]);

        foreach ($sessionCart as $item) {
            $cartItem = CartItem::where('cart_id', $cart->id)
                ->where('product_id', $item['product_id'])
                ->where('product_variant_id', $item['product_variant_id'])
                ->where('color_id', $item['color_id'])
                ->where('size_id', $item['size_id'])
                ->where('warranty_id', $item['warranty_id'])
                ->first();

            $product = Product::find($item['product_id']);
            if (!$product || $product->status !== 'active') continue;

            $variant = $item['product_variant_id'] ? ProductVariant::find($item['product_variant_id']) : null;
            $maxStock = $variant ? $variant->stock_quantity : $product->stock_quantity;

            if ($cartItem) {
                $mergedQty = min($cartItem->quantity + $item['quantity'], $maxStock);
                $cartItem->update(['quantity' => $mergedQty]);
            } else {
                $qty = min($item['quantity'], $maxStock);
                CartItem::create([
                    'cart_id' => $cart->id,
                    'product_id' => $item['product_id'],
                    'product_variant_id' => $item['product_variant_id'],
                    'color_id' => $item['color_id'],
                    'size_id' => $item['size_id'],
                    'warranty_id' => $item['warranty_id'],
                    'quantity' => $qty,
                ]);
            }
        }

        session()->forget('cart');
    }
}
