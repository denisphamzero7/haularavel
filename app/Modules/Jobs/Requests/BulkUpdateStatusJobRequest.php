<?php
namespace App\Modules\Jobs\Requests;

use App\Modules\Jobs\Enums\JobStatusEnum;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkUpdateStatusJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array',
            'ids.*' => 'required|integer|exists:itjobs,id',
            'status' => [
                'required',
                'string',
                Rule::enum(JobStatusEnum::class)
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Danh sách ID không được để trống.',
            'status.required' => 'Trạng thái không được để trống.',
        ];
    }
}
