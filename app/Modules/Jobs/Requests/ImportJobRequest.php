<?php
namespace App\Modules\Jobs\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ImportJobRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file|mimes:xlsx,xls,csv',
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'File không được để trống.',
            'file.mimes' => 'File phải có định dạng xlsx, xls hoặc csv.',
        ];
    }
}
