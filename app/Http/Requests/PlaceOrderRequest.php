<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PlaceOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Middleware handles auth check
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'receiver_name' => 'required|string|max:255',
            'receiver_phone' => ['required', 'string', 'regex:/^(0[35789])[0-9]{8}$/'],
            'receiver_email' => 'required|email|max:255',
            'province_code' => 'required|string|exists:provinces,code',
            'district_code' => 'required|string|exists:districts,code',
            'ward_code' => 'required|string|exists:wards,code',
            'address' => 'required|string|max:255',
            'note' => 'nullable|string|max:500',
            'payment_method' => 'required|string|in:cod,bank_transfer,installment,payos',
            'installment_months' => 'required_if:payment_method,installment|nullable|integer|in:3,6,12',
            'receipt_type' => 'required|string|in:paper,electronic',
            'receipt_email' => 'required_if:receipt_type,electronic|nullable|email|max:255',
            'coupon_code' => 'nullable|string|max:50',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'receiver_name.required' => 'Họ và tên người nhận là bắt buộc.',
            'receiver_phone.required' => 'Số điện thoại người nhận là bắt buộc.',
            'receiver_phone.regex' => 'Số điện thoại không đúng định dạng di động Việt Nam (ví dụ: 0987654321).',
            'receiver_email.required' => 'Email liên hệ là bắt buộc.',
            'receiver_email.email' => 'Địa chỉ email không hợp lệ.',
            'province_code.required' => 'Vui lòng chọn Tỉnh/Thành phố.',
            'province_code.exists' => 'Tỉnh/Thành phố đã chọn không hợp lệ.',
            'district_code.required' => 'Vui lòng chọn Quận/Huyện.',
            'district_code.exists' => 'Quận/Huyện đã chọn không hợp lệ.',
            'ward_code.required' => 'Vui lòng chọn Phường/Xã.',
            'ward_code.exists' => 'Phường/Xã đã chọn không hợp lệ.',
            'address.required' => 'Số nhà, tên đường cụ thể là bắt buộc.',
            'payment_method.required' => 'Vui lòng chọn phương thức thanh toán.',
            'payment_method.in' => 'Phương thức thanh toán không được hỗ trợ.',
            'installment_months.required_if' => 'Vui lòng chọn kỳ hạn trả góp.',
            'installment_months.in' => 'Kỳ hạn trả góp phải là 3, 6, hoặc 12 tháng.',
            'receipt_type.required' => 'Vui lòng chọn hình thức nhận biên lai.',
            'receipt_email.required_if' => 'Vui lòng nhập email nhận biên lai điện tử.',
            'receipt_email.email' => 'Email nhận biên lai không đúng định dạng.',
            'coupon_code.exists' => 'Mã giảm giá đã chọn không tồn tại.',
        ];
    }
}
