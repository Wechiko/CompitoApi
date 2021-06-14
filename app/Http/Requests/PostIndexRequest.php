<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class postInddexRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
            if ($this->has('with')) {
                $this->merge(['with' => explode(',', $this->with)]);
            }
    }
}
