<?php

namespace App\Modules\Category\Services;

use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Category\Exports\CategoriesExport;
use App\Modules\Category\Imports\CategoriesImport;
use App\Modules\Category\Models\Category;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CategoryService
{
   

  

    /**
     * Thống kê số lượng danh mục theo trạng thái.
     *
     * @param array $filters Bộ lọc tìm kiếm và trạng thái.
     * @return array Mảng chứa tổng số, số lượng active và inactive.
     */
    public function stats(array $filters): array
    {
        $base = Category::filter($filters);

        return [
            'total' => (clone $base)->count(),
            'active' => (clone $base)->where('status', StatusEnum::Active->value)->count(),
            'inactive' => (clone $base)->where('status', StatusEnum::Inactive->value)->count(),
        ];
    }

    /**
     * Lấy danh sách danh mục có phân trang và eager loading.
     *
     * @param array $filters Bộ lọc tìm kiếm, trạng thái, ngày tạo.
     * @param int $limit Số bản ghi trên mỗi trang.
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function index(array $filters, int $limit)
    {
        return Category::filter($filters)
            ->paginate($limit);
    }

    

    /**
     * Lấy thông tin chi tiết của một danh mục.
     *
     * @param Category $category Đối tượng danh mục.
     * @return Category Danh mục kèm theo thông tin cha, con và người tạo.
     */
    public function show(Category $category): Category
    {
        return $category->load(['creator', 'editor', 'parent']);
    }

    /**
     * Tạo mới một danh mục.
     *
     * @param array $validated Dữ liệu đã qua kiểm soát.
     * @return Category Danh mục vừa tạo.
     * @throws \Throwable
     */
    public function store(array $validated): Category
    {
        return DB::transaction(function () use ($validated) {
            $data = $validated;
            $data['organization_id'] = $this->resolveCurrentOrganizationId();
            
            $category = Category::create($data);

            return $category->load(['creator', 'editor', 'parent']);
        });
    }

    /**
     * Cập nhật thông tin danh mục.
     *
     * @param Category $category Đối tượng danh mục cần sửa.
     * @param array $validated Dữ liệu mới.
     * @return array Danh mục sau khi cập nhật.
     * @throws \Throwable
     */
    public function update(Category $category, array $validated): array
    {
        try {
            return DB::transaction(function () use ($category, $validated) {
                $category->update($validated);
                return [
                    'ok' => true,
                    'category' => $category->load(['creator', 'editor', 'parent'])
                ];
            });
        } catch (\Exception $e) {
            return [
                'ok' => false,
                'message' => 'Lỗi cập nhật: ' . $e->getMessage(),
                'code' => 500,
                'error_code' => 'UPDATE_ERROR'
            ];
        }
    }

    public function destroy(Category $category): void
    {
        $category->delete();
    }

    public function bulkDestroy(array $ids): void
    {
        DB::transaction(function () use ($ids) {
            Category::where('organization_id', $this->resolveCurrentOrganizationId())
                ->whereIn('id', $ids)
                ->get()
                ->each
                ->delete();
        });
    }

    public function bulkUpdateStatus(array $ids, string $status): void
    {
        Category::where('organization_id', $this->resolveCurrentOrganizationId())
            ->whereIn('id', $ids)
            ->update(['status' => $status]);
    }

    public function export(array $filters): BinaryFileResponse
    {
        return Excel::download(
            new CategoriesExport($filters, $this->resolveCurrentOrganizationId()), 
            'categories.xlsx'
        );
    }

    public function import($file): void
    {
        Excel::import(new CategoriesImport($this->resolveCurrentOrganizationId()), $file);
    }

    private function resolveCurrentOrganizationId(): int
    {
        $organizationId = function_exists('getPermissionsTeamId') ? getPermissionsTeamId() : null;

        if (! is_numeric($organizationId) || (int) $organizationId <= 0) {
            throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Không xác định được tổ chức làm việc hiện tại.');
        }

        return (int) $organizationId;
    }
}