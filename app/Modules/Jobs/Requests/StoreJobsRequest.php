<?php
namespace App\Modules\Jobs\Requests;

use App\Modules\Jobs\Enums\JobStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreJobsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules():array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => [
                'nullable',
                'string',
                Rule::enum(JobStatusEnum::class)
            ],
            'due_at' => 'nullable|date_format:Y-m-d H:i:s',
            'is_notified' => 'nullable|boolean',
        ];
    }

    public function validated($key = null, $default = null)
    {
        $data = parent::validated($key, $default);
        $data['status'] = $data['status'] ?? JobStatusEnum::Draft->value;
        return $data;
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tiêu đề công việc không được để trống.'
        ];
    }
}
