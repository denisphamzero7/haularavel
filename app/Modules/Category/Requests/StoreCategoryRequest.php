<?php

namespace App\Modules\Category\Requests;

use App\Modules\Core\Enums\StatusEnum;
use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:65535',
            'status' => ['required', StatusEnum::rule()],
            'parent_id' => 'nullable|integer|exists:categories,id',
            'sort_order' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tên danh mục không được để trống.',
            'title.string' => 'Tên danh mục phải là chuỗi ký tự.',
            'title.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            'status.required' => 'Trạng thái không được để trống.',
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
                'description' => 'Tiêu đề của danh mục (tên hiển thị).',
                'example' => 'Điện thoại & Máy tính bảng',
            ],
            'description' => [
                'description' => 'Mô tả chi tiết về mục đích hoặc nội dung của danh mục.',
                'example' => 'Chuyên mục chứa các sản phẩm điện tử, di động và phụ kiện đi kèm.',
            ],
            'status' => [
                'description' => 'Trạng thái hoạt động của danh mục (active: cho phép hiển thị, inactive: tạm ẩn).',
                'example' => 'active',
            ],
            'parent_id' => [
                'description' => 'ID của danh mục cấp trên. Nếu gửi null hoặc không gửi, danh mục sẽ là danh mục gốc (root).',
                'example' => null,
            ],
            'sort_order' => [
                'description' => 'Thứ tự ưu tiên hiển thị (số càng nhỏ đứng càng trước).',
                'example' => 1,
            ],
        ];
    }
}
