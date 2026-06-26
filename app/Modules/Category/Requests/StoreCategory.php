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
            'status' => ['required', StatusEnum::rule()],
            'description' => 'nullable|string|max:65535',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Tên danh mục không được để trống.',
        ];
    }
}
