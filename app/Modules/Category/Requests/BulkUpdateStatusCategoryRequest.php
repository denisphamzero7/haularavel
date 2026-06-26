<?php

namespace App\Modules\Category\Requests;

use App\Modules\Core\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class BulkUpdateStatusCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => 'required|array|min:1',
            'ids.*' => 'exists:categories,id',
            'status' => ['required', StatusEnum::rule()],
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Bạn chưa chọn danh mục nào.',
            'ids.*.exists' => 'Một trong các danh mục không tồn tại.',
            'status.required' => 'Trạng thái không được để trống.',
            'status.in' => 'Trạng thái không hợp lệ.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'ids' => [
                'description' => 'Mảng chứa danh sách ID các danh mục cần cập nhật trạng thái.',
                'example' => [1, 2, 5],
            ],
            'status' => [
                'description' => 'Trạng thái mới áp dụng cho tất cả danh mục đã chọn (active hoặc inactive).',
                'example' => 'inactive',
            ],
        ];
    }
}
