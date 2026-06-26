<?php

namespace App\Modules\Jobs\Requests;


use App\Modules\Jobs\Enums\JobStatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateStatusJobsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:jobs,id',
            'status' => ['required', JobStatusEnum::rule()],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Bạn chưa chọn công việc nào.',
            'ids.*.exists' => 'Một trong các công việc không tồn tại.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'ids' => [
                'description' => 'Mảng chứa danh sách ID các công việc cần cập nhật trạng thái.',
                'example' => [1, 2, 5],
            ],
            'status' => [
                'description' => 'Trạng thái mới áp dụng cho tất cả danh mục đã chọn (active hoặc inactive).',
                'example' => 'inactive',
            ],
        ];
    }
}
