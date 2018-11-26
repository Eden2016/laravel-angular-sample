<?php

namespace App\Http\Requests;

use Auth;
use App\Http\Requests\Request;

class OverwatchHeroRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::user()->hasRole('ow_heroes');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|max:35',
            'info' => 'required',
            'role' => 'required|numeric',
            'active' => 'required|numeric',
            'image' => ($this->route('id') ? '' : 'required|' ) . 'file'
        ];
    }
}
