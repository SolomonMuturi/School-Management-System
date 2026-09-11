<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class SettingUpdate extends FormRequest
{

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
        $rules = [
            'system_name' => 'required|string|min:10',
            'system_title' => 'nullable|string|max:190',
            'current_session' => 'required|regex:/^\d{4}-\d{4}$/',
            'term_ends' => 'nullable|string|max:60',
            'term_begins' => 'nullable|string|max:60',
            'phone' => 'nullable|string|max:30',
            'address' => 'required|string|min:15',
            'system_email' => 'sometimes|nullable|email',
            'alt_email' => 'sometimes|nullable|email',
            'lock_exam' => 'required',
            'logo' => 'sometimes|nullable|image|mimes:jpeg,gif,png,jpg|max:2048',
        ];

        foreach ($this->all() as $key => $value) {
            if (Str::startsWith($key, 'next_term_fees_')) {
                $rules[$key] = 'nullable|numeric';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'current_session.regex' => 'The Current Session must be in the format YYYY-YYYY (e.g. 2026-2027).',
        ];
    }

    public function attributes()
    {
        return  [
            'system_name' => 'School Name',
            'system_email' => 'School Email',
            'current_session' => 'Current Session',
        ];
    }

}
