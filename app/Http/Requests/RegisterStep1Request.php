<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterStep1Request extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'username' => ['nullable', 'string', 'alpha_num', 'min:3', 'max:50', 'unique:users,username'],
            'email' => ['required_without:username', 'nullable', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'policy' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.alpha_num' => 'Tên tài khoản chỉ được chứa chữ cái và số.',
            'username.min' => 'Tên tài khoản phải có ít nhất 3 ký tự.',
            'username.max' => 'Tên tài khoản không được vượt quá 50 ký tự.',
            'username.unique' => 'Tên tài khoản này đã được sử dụng.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.required_without' => 'Vui lòng nhập địa chỉ email.',
            'email.email' => 'Email không đúng định dạng.',
            'email.unique' => 'Email này đã được sử dụng.',
            'password.required' => 'Mật khẩu là bắt buộc.',
            'password.min' => 'Mật khẩu phải có ít nhất 8 ký tự.',
            'password.confirmed' => 'Mật khẩu nhập lại không khớp.',
            'policy.required' => 'Bạn phải đồng ý với chính sách và điều khoản.',
            'policy.accepted' => 'Bạn phải đồng ý với chính sách và điều khoản.',
        ];
    }
}
