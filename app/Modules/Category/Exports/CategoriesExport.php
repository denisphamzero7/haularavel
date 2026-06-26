<?php

namespace App\Modules\Category\Exports;

use App\Modules\Category\Models\Category;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CategoriesExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = [],
        protected ?int $organizationId = null
    ) {}

    /**
     * Xuất danh sách danh mục kèm thông tin người tạo/cập nhật.
     */
    public function collection()
    {
        // Khởi tạo query và lọc theo tổ chức
        $query = Category::query()->where('organization_id', $this->organizationId);

        // Áp dụng bộ lọc nếu có (nếu $filters trống, scopeFilter sẽ không lọc thêm gì)
        $categories = $query->with(['creator', 'editor'])
            ->filter($this->filters)
            ->get();

        return $categories->map(fn ($category) => [
            'id' => $category->id,
            'title' => $category->title,
            'description' => $category->description,
            'status' => $category->status,
            'creator' => $category->creator?->name ?? 'N/A',
            'editor' => $category->editor?->name ?? 'N/A',
            'created_at' => $category->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $category->updated_at?->format('d/m/Y H:i:s'),
        ]);
    }

    public function headings(): array
    {
        return [
            'ID',
            'Title',
            'Description',
            'Status',
            'Created By',
            'Updated By',
            'Created At',
            'Updated At',
        ];
    }
}
