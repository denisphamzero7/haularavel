<?php

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BulkDestroyCategoryRequest extends FormRequest
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
        ];
    }

    public function messages(): array
    {
        return [
            'ids.required' => 'Bạn chưa chọn danh mục nào.',
            'ids.*.exists' => 'Một trong các danh mục không tồn tại.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'ids' => [
                'description' => 'Danh sách ID của các danh mục bạn muốn xóa vĩnh viễn.',
                'example' => [10, 15, 22],
            ],
        ];
    }
}
