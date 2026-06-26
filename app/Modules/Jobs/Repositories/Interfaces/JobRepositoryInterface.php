<?php
namespace App\Modules\Jobs\Repositories\Interfaces;

interface JobRepositoryInterface {
    public function getAll(array $filters);
    public function store(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function bulkDelete(array $ids);
    public function bulkUpdateStatus(array $ids, string $status);
}
