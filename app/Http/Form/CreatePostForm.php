<?php

namespace App\Http\Form;

use App\Exceptions\ThrottleException;
use App\Reply;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CreatePostForm extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('create', new Reply);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'body' => 'required|spamfree'
        ];
    }

    /**
     * @throws ThrottleException
     */
    protected function failedAuthorization()
    {
        throw new ThrottleException('You are replying too quickly. Please hole 1min');
    }


}
