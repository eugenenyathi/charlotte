<?php

namespace App\Http\Requests;

use App\Constants\RoommatePreferenceConstants;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoommatePreferenceRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'student_id' => ['required', 'string', 'max:9', 'regex: /^L0\d{6}[A-Z]{1}$/u'],
            'question_1' => ['required', 'string', 'min:2', 'max:3', Rule::in(RoommatePreferenceConstants::ANSWERS)],
            'question_2' => ['required', 'string', 'min:2', 'max:3', Rule::in(RoommatePreferenceConstants::ANSWERS)],
            // 'question_3' => ['required', 'string', 'min:2', 'max:3', Rule::in(RoommatePreferenceConstants::ANSWERS)],
        ];
    }
}
