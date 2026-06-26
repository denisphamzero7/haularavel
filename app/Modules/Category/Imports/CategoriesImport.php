<?php

namespace App\Modules\Category\Imports;

use App\Modules\Category\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoriesImport implements ToModel, WithHeadingRow
{
    public function __construct(
        private readonly int $organizationId
    ) {}

    /**
     * Nhập danh mục từ file Excel.
     */
    public function model(array $row)
    {
        return new Category([
            'title' => $row['title'] ?? '',
            'description' => $row['description']  ?? null,
            'organization_id' => $this->organizationId,
            'status' => $row['status'] ?? 'active',
        ]);
    }
}
