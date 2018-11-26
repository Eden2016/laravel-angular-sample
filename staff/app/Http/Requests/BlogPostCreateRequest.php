<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class BlogPostCreateRequest extends Request
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
        if(request()->get('save_as_draft', 0) == 1) return [
            'title' => 'required|max:255',
            'games' => 'array',
            'client' => 'required',
            'tags' => 'string|max:1000',
        ];

        return [
            'title' => 'required|max:255',
            'games' => 'array',
            'client' => 'required',
            'tags' => 'string|max:1000',
            'headline' => 'required',
            'thumb' => 'required'
        ];
    }
}
