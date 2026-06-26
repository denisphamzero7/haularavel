<?php

namespace App\Modules\Jobs;

use App\Http\Controllers\Controller;
use App\Modules\Jobs\Models\ITJob;
use App\Modules\Jobs\Requests\StoreJobsRequest;
use App\Modules\Jobs\Requests\UpdateJobRequest;
use App\Modules\Jobs\Requests\BulkDestroyJobRequest;
use App\Modules\Jobs\Requests\BulkUpdateStatusJobRequest;
use App\Modules\Jobs\Requests\ChangeStatusJobRequest;
use App\Modules\Jobs\Requests\ImportJobRequest;
use App\Modules\Jobs\Resources\JobCollection;
use App\Modules\Jobs\Resources\JobResource;
use App\Modules\Jobs\Services\JobService;
use App\Modules\Core\Requests\FilterRequest;

class JobController extends Controller
{
    public function __construct(private JobService $jobService) {}

    /**
     * Thống kê công việc
     */
    public function stats(FilterRequest $request)
    {
        return $this->success($this->jobService->stats($request->all()));
    }

    /**
     * Danh sách công việc
     */
    public function index(FilterRequest $request)
    {
        $jobs = $this->jobService->index($request->all(), (int) ($request->limit ?? 10));
        return $this->successCollection(new JobCollection($jobs));
    }

    /**
     * Chi tiết công việc
     */
    public function show(ITJob $job)
    {
        $job = $this->jobService->show($job);
        return $this->successResource(new JobResource($job));
    }

    /**
     * Tạo công việc mới
     */
    public function store(StoreJobsRequest $request)
    {
        $data = $request->validated();
        try {
            $job = $this->jobService->store($data);
            return $this->successResource(new JobResource($job), 'Công việc đã được tạo thành công!', 201);
        } catch (\Throwable $th) {
            return $this->error('Tạo công việc thất bại!', 500, null, $th->getMessage());
        }
    }

    /**
     * Cập nhật công việc
     */
    public function update(UpdateJobRequest $request, ITJob $job)
    {
        $result = $this->jobService->update($job, $request->validated());
        if (!$result['ok']) {
            return $this->error($result['message'], $result['code'], null, $result['error_code']);
        }
        return $this->successResource(new JobResource($result['job']), 'Công việc đã được cập nhật!');
    }

    /**
     * Xóa công việc
     */
    public function destroy(ITJob $job)
    {
        $this->jobService->destroy($job);
        return $this->success(null, 'Công việc đã được xóa!');
    }

    /**
     * Xóa hàng loạt công việc
     */
    public function bulkDestroy(BulkDestroyJobRequest $request)
    {
        $this->jobService->bulkDestroy($request->ids);
        return $this->success(null, 'Đã xóa thành công các công việc được chọn!');
    }

    /**
     * Cập nhật trạng thái hàng loạt
     */
    public function bulkUpdateStatus(BulkUpdateStatusJobRequest $request)
    {
        $this->jobService->bulkUpdateStatus($request->ids, $request->status);
        return $this->success(null, 'Cập nhật trạng thái thành công các công việc được chọn!');
    }

    /**
     * Xuất danh sách công việc (Excel)
     */
    public function export(FilterRequest $request)
    {
        return $this->jobService->export($request->all());
    }

    /**
     * Nhập danh sách công việc
     */
    public function import(ImportJobRequest $request)
    {
        $this->jobService->import($request->file('file'));
        return $this->success(null, 'Import công việc thành công.');
    }

    /**
     * Thay đổi trạng thái công việc
     */
    public function changeStatus(ChangeStatusJobRequest $request, ITJob $job)
    {
        $result = $this->jobService->update($job, ['status' => $request->status]);
        return $this->successResource(new JobResource($result['job']), 'Cập nhật trạng thái thành công!');
    }
}

