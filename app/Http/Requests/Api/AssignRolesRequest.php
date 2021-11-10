<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use App\Http\Traits\RespondsWithHttpStatus;

class AssignRolesRequest extends FormRequest
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
            'user_id'     =>'required|exists:users,id',
            'roles' => 'required',
        ];
    }

    /**
     * @param Validator $validator\
     */
    public function failedValidation(Validator $validator): void
    {
        $errors = $validator->errors();

        throw new HttpResponseException($this->failure($errors->first(),400));
    }
}
