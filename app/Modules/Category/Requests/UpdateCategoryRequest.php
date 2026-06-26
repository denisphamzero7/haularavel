<?php

namespace App\Modules\Category\Requests;

use App\Modules\Core\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string|max:65535',
            'status' => ['sometimes', StatusEnum::rule()],
            'parent_id' => 'nullable|integer|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'title.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'status.in' => 'Trạng thái không hợp lệ.',
            'parent_id.integer' => 'ID danh mục cha phải là số nguyên.',
            'parent_id.exists' => 'Danh mục cha không tồn tại.',
            'sort_order.integer' => 'Thứ tự hiển thị phải là số nguyên.',
            'sort_order.min' => 'Thứ tự hiển thị không được nhỏ hơn 0.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'title' => [
                'description' => 'Cập nhật tiêu đề mới cho danh mục.',
                'example' => 'Gia dụng & Đời sống',
            ],
            'description' => [
                'description' => 'Cập nhật nội dung mô tả mới.',
                'example' => 'Các sản phẩm phục vụ nhu cầu hàng ngày trong gia đình.',
            ],
            'status' => [
                'description' => 'Thay đổi trạng thái hoạt động.',
                'example' => 'inactive',
            ],
            'parent_id' => [
                'description' => 'Thay đổi danh mục cha (di chuyển danh mục trong cây).',
                'example' => 2,
            ],
            'sort_order' => [
                'description' => 'Thay đổi thứ tự hiển thị ưu tiên.',
                'example' => 10,
            ],
        ];
    }
}
