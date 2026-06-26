<?php

namespace App\Modules\Jobs\Exports;

use App\Modules\Jobs\Models\ITJob;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class JobsExport implements FromCollection, WithHeadings
{
    public function __construct(
        protected array $filters = [],
        protected ?int $organizationId = null
    ) {}

    /**
     * Xuất danh sách công việc kèm thông tin người tạo/cập nhật.
     */
    public function collection()
    {
        // Khởi tạo query và lọc theo tổ chức
        $query = ITJob::query()->where('organization_id', $this->organizationId);

        // Áp dụng bộ lọc nếu có (nếu $filters trống, scopeFilter sẽ không lọc thêm gì)
        $Itjobs = $query->with(['creator', 'editor'])
            ->filter($this->filters)
            ->get();

        return $Itjobs->map(fn ($job) => [
            'id' => $job->id,
            'title' => $job->title,
            'description' => $job->description,
            'status' => $job->status?->value ?? 'N/A',
            'creator' => $job->creator?->name ?? 'N/A',
            'editor' => $job->editor?->name ?? 'N/A',
            'created_at' => $job->created_at?->format('d/m/Y H:i:s'),
            'updated_at' => $job->updated_at?->format('d/m/Y H:i:s'),
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
