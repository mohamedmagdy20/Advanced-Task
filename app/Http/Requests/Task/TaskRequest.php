<?php

namespace App\Http\Requests\Task;

use App\Helper\ApiResponser;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class TaskRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'due_date' => [
                'required',
                'date',
                'after_or_equal:'. now()->timezone(auth()->user()->timezone)->startOfDay()
            ],
            'priority'=>'required|in:high,medium,low'
        ];
    }


    
    public function messages(){
        return [
            'title.required' => 'The Title field is required.',
            'due_date.required' => 'The Due Date field is required.',
            'due_date.date' => 'The Due Date Must Be Type Date.',
            'due_date.after_or_equal' => 'The Due Date Must Be Future Date.',

        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponser::validationErrorResponse($validator->errors())
        );
    }

}
