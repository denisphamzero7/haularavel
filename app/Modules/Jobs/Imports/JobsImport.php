<?php

namespace App\Modules\Jobs\Imports;

use App\Modules\Jobs\Models\ITJob;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class JobsImport implements ToModel, WithHeadingRow
{
    public function __construct(
        private readonly int $organizationId
    ) {}

    /**
     * Nhập công việc từ file Excel.
     */
    public function model(array $row)
    {
        return new ITJob([
            'title' => $row['title'] ?? '',
            'description' => $row['description'] ?? null,
            'organization_id' => $this->organizationId,
            'status' => $row['status'] ?? 'draft',
        ]);
    }
}
