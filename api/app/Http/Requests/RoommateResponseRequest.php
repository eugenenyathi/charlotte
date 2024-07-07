<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RoommateResponseRequest extends FormRequest
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
        //studentID being the roommate who has sent a response
        return [
            'studentID' => ['required', 'string', 'max:9', 'regex: /^L0\d{6}[A-Z]{1}$/u'],
            'requesterID' => ['required', 'string', 'max:9', 'regex: /^L0\d{6}[A-Z]{1}$/u'],
            'response' => ['required', 'string', 'min:2', 'max:3']
        ];
    }
}
