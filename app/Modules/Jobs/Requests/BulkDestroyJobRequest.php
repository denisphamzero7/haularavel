<?php
namespace App\Modules\Jobs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyJobRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Danh sách ID không được để trống.',
            'ids.array' => 'IDs phải là một mảng.',
            'ids.*.exists' => 'Một hoặc nhiều công việc không tồn tại.',
        ];
    }
}
