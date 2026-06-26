<?php
namespace App\Repositories\Eloquent;

use App\Modules\Jobs\Models\ITJob;
use App\Modules\Jobs\Repositories\Interfaces\JobRepositoryInterface;

class JobRepository implements JobRepositoryInterface {
    public function getAll(array $filters) {
        $query = ITJob::query();

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->where('title', 'like', '%' . $filters['search'] . '%');
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 15);
    }

    public function store(array $data) {
        return ITJob::create($data);
    }

    public function update($id, array $data) {
        $job = ITJob::findOrFail($id);
        $job->update($data);
        return $job;
    }

    public function delete($id) {
        return ITJob::destroy($id);
    }

    public function bulkDelete(array $ids) {
        return ITJob::whereIn('id', $ids)->delete();
    }

    public function bulkUpdateStatus(array $ids, string $status) {
        return ITJob::whereIn('id', $ids)->update(['status' => $status]);
    }
}