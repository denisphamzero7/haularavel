<?php

namespace App\Modules\Jobs\Services;

use App\Modules\Jobs\Models\ITJob;
use App\Modules\Jobs\Exports\JobsExport;
use App\Modules\Jobs\Imports\JobsImport;
use App\Modules\Core\Enums\StatusEnum;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use App\Modules\Jobs\Events\JobCreated;
use App\Modules\Jobs\Events\JobUpdatedEvent;

class JobService
{
    public function __construct()
    {
    }

    /**
     * Thống kê số lượng công việc theo trạng thái.
     */
    public function stats(array $filters): array
    {
        $base = ITJob::filter($filters);

        return [
            'total' => (clone $base)->count(),
            'draft' => (clone $base)->where('status', 'draft')->count(),
            'published' => (clone $base)->where('status', 'published')->count(),
            'archived' => (clone $base)->where('status', 'archived')->count(),
        ];
    }

    /**
     * Lấy danh sách công việc có phân trang.
     */
    public function index(array $filters, int $limit)
    {
       return ITJob::where('organization_id', $this->resolveCurrentOrganizationId())
            ->filter($filters)
            ->with(['creator', 'editor'])
            ->paginate($limit);
    }

    /**
     * Lấy chi tiết công việc.
     */
    public function show(ITJob $job): ITJob
    {
        return $job->load(['creator', 'editor']);
    }

    /**
     * Tạo công việc mới.
     */
   public function store(array $data): ITJob
    {
        // 1. Thực hiện lưu database trong Transaction
        $job = DB::transaction(function () use ($data) {
            $data['organization_id'] = $this->resolveCurrentOrganizationId();
            return ITJob::create($data);
        });

        // 2. CHỈ SAU KHI Transaction thành công mới đẩy vào Queue/Horizon
        broadcast(new JobUpdatedEvent('job-created', $job->toArray()));

        return $job;
    }

    /**
     * Cập nhật công việc.
     */
  public function update(ITJob $job, array $validated): array
    {
        try {
            // 1. Cập nhật trong Transaction
            $updatedJob = DB::transaction(function () use ($job, $validated) {
                $job->update($validated);
                return $job->load(['creator', 'editor']);
            });

            // 2. Chuyển lệnh broadcast ra ngoài
            broadcast(new JobUpdatedEvent('job-updated', $updatedJob->toArray()));

            return [
                'ok' => true,
                'job' => $updatedJob
            ];
        } catch (\Exception $e) {
            return [
                'ok' => false,
                'message' => 'Lỗi cập nhật: ' . $e->getMessage(),
                'code' => 500,
                'error_code' => 'UPDATE_ERROR'
            ];
        }
    }

    /**
     * Xóa công việc.
     */
    public function destroy(ITJob $job): void
    {
        $id = $job->id;
        $job->delete($id);

        // Realtime: Báo Client xóa item này khỏi danh sách
        broadcast(new JobUpdatedEvent('job-deleted', $job->toArray()));
    }

    /**
     * Xóa hàng loạt công việc.
     */
  public function bulkDestroy(array $ids): void
{
    // 1. Thực hiện xóa trong Transaction
    DB::transaction(function () use ($ids) {
        ITJob::where('organization_id', $this->resolveCurrentOrganizationId())
            ->whereIn('id', $ids)
            ->get()
            ->each
            ->delete();
    });

    // 2. Chuyển lệnh broadcast ra ngoài, DB cam kết xóa thành công thì mới báo Realtime
    broadcast(new JobUpdatedEvent('job-bulk-deleted', ['ids' => $ids]));
}

    /**
     * Cập nhật trạng thái hàng loạt.
     */
    public function bulkUpdateStatus(array $ids, string $status): void
    {
        if ($status === \App\Modules\Jobs\Enums\JobStatusEnum::Completed->value) {
            $updateData['is_notified'] = 1;
        }
        ITJob::where('organization_id', $this->resolveCurrentOrganizationId())
            ->whereIn('id', $ids)
            ->update(['status' => $status]);

        // Realtime: Báo Client cập nhật lại trạng thái cho danh sách ID này
        broadcast(new JobUpdatedEvent('job-bulk-status-updated', ['ids' => $ids, 'status' => $status]));
    }

    /**
     * Xuất danh sách công việc (Excel).
     */
    public function export(array $filters): BinaryFileResponse
    {
        return Excel::download(
            new JobsExport($filters, $this->resolveCurrentOrganizationId()),
            'jobs.xlsx'
        );
    }

    /**
     * Nhập danh sách công việc.
     */
    public function import($file): void
    {
        Excel::import(new JobsImport($this->resolveCurrentOrganizationId()), $file);
    }

    private function resolveCurrentOrganizationId(): int
    {
        $organizationId = function_exists('getPermissionsTeamId') ? getPermissionsTeamId() : null;

        if (! is_numeric($organizationId) || (int) $organizationId <= 0) {
            throw new \Exception('Organization ID không hợp lệ');
        }

        return (int) $organizationId;
    }
}
