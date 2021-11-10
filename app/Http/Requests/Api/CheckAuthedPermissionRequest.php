<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use App\Http\Traits\RespondsWithHttpStatus;
use Illuminate\Http\Exceptions\HttpResponseException;

class CheckAuthedPermissionRequest extends FormRequest
{

    use RespondsWithHttpStatus;
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
            //
            'permission' => 'required|exists:permissions,id',
        ];
    }

    /**
     * @param Validator $validator
     */
    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException($this->failure($errors->first(),400));
    }
}
