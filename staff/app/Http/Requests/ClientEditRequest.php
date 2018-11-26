<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class ClientEditRequest extends Request
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
            'name' => 'required|max:255',
            'email' => 'required|email|max:255',
            'headline_width' => 'required|integer|min:1',
            'headline_height' => 'required|integer|min:1',
            'thumb_width' => 'required|integer|min:1',
            'thumb_height' => 'required|integer|min:1'
        ];
    }
}
