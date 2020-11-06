<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FindEmailRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'exists:users'
        ];
    }

    public function messages() {
        return [
            'exists' => 'メールの送信に失敗しました。メールアドレスが正しいか確認してください。'
        ];
    }
}
