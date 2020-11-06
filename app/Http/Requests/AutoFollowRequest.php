<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AutoFollowRequest extends FormRequest
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
            'targetList' => 'required',
            'account_id' => 'required',
        ];
    }
    public function messages() {
        return [
            'targetList.required' => 'ターゲットリストが登録されていません。',
            'account_id.required'  => '自動機能を利用するアカウントが未選択です。',
        ];
    }
}
