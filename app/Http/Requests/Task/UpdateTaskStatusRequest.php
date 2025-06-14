<?php

namespace App\Http\Requests\Task;

use App\Helper\ApiResponser;
use App\Models\Task;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTaskStatusRequest extends FormRequest
{
      protected $task;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $this->task = Task::find($this->route('id'));
        
        return $this->task && $this->task->user_id == auth()->id();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
     public function rules()
    {
        return [
            'status' => [
                'required',
                 Rule::in(['pending', 'in_progress', 'completed', 'overdue'])
            ],
        ];
    }


     public function messages()
    {
        return [
            'status.required'=>'Status Field is Required',
            'status.in' => 'Invalid status value',
        ];
    }
}
