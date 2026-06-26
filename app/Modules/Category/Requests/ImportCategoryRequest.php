<?php

namespace App\Modules\Category\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|mimes:xlsx,xls,csv',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Vui lòng chọn file để nhập liệu.',
            'file.mimes' => 'File phải có định dạng xlsx, xls hoặc csv.',
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'file' => [
                'description' => 'File Excel dữ liệu danh mục (.xlsx, .xls, .csv). Cấu trúc cột phải khớp với chuẩn hệ thống.',
            ],
        ];
    }
}
