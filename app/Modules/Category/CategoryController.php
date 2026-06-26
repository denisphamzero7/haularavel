<?php

namespace App\Modules\Category;

use App\Http\Controllers\Controller;
use App\Modules\Core\Requests\FilterRequest;
use App\Modules\Core\Resources\PublicOptionResource;
use App\Modules\Category\Models\Category;
use App\Modules\Category\Requests\BulkDestroyCategoryRequest;
use App\Modules\Category\Requests\BulkUpdateStatusCategoryRequest;
use App\Modules\Category\Requests\ChangeStatusCategoryRequest;
use App\Modules\Category\Requests\ImportCategoryRequest;
use App\Modules\Category\Requests\StoreCategoryRequest;
use App\Modules\Category\Requests\UpdateCategoryRequest;
use App\Modules\Category\Resources\CategoryCollection;
use App\Modules\Category\Resources\CategoryResource;

use App\Modules\Category\Services\CategoryService;
use Illuminate\Http\Request;

/**
 * @group Category - Danh mục
 * @header X-Organization-Id ID tổ chức cần làm việc (bắt buộc với endpoint yêu cầu auth). Example: 1
 *
 * Quản lý danh mục hệ thống (cấu trúc cây parent_id): danh sách, thống kê, chi tiết, tạo, cập nhật, xóa, thao tác hàng loạt, xuất/nhập excel.
 */
class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService) {}

    /**
     * Thống kê danh mục
     *
     * Lấy các số liệu tổng quan: tổng số, đang hoạt động, ngừng hoạt động.
     *
     * @queryParam search string Từ khóa tìm kiếm.
     * @queryParam status string Lọc theo trạng thái.
     * @queryParam from_date date Lọc từ ngày tạo (Y-m-d).
     * @queryParam to_date date Lọc đến ngày tạo (Y-m-d).
     *
     * @response 200 {"success": true, "data": {"total": 100, "active": 80, "inactive": 20}}
     */
    public function stats(FilterRequest $request)
    {
        return $this->success($this->categoryService->stats($request->all()));
    }

    /**
     * Danh sách danh mục
     *
     * Lấy danh sách danh mục có phân trang và lọc theo nhiều tiêu chí.
     *
     * @queryParam search string Từ khóa tìm kiếm theo tiêu đề. Example: công nghệ
     * @queryParam status string Lọc trạng thái: active, inactive. Example: active
     * @queryParam sort_by string Sắp xếp theo: id, title, sort_order, created_at. Example: sort_order
     * @queryParam sort_order string Thứ tự: asc, desc. Example: asc
     * @queryParam limit integer Số bản ghi mỗi trang (1-100). Example: 15
     *
     * @apiResourceCollection App\Modules\Category\Resources\CategoryCollection
     * @apiResourceModel App\Modules\Category\Models\Category paginate=15
     * @apiResourceAdditional success=true
     */
    public function index(FilterRequest $request)
    {
        $categories = $this->categoryService->index($request->all(), (int) ($request->limit ?? 10));

        return $this->successCollection(new CategoryCollection($categories));
    }

    /**
     * Chi tiết danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     *
     * @apiResource App\Modules\Category\Resources\CategoryResource
     * @apiResourceModel App\Modules\Category\Models\Category with=parent
     * @apiResourceAdditional success=true
     */
    public function show(Category $category)
    {
        $category = $this->categoryService->show($category);

        return $this->successResource(new CategoryResource($category));
    }

    /**
     * Tạo danh mục mới
     *
     * @bodyParam title string required Tiêu đề danh mục. Example: Tin tức mới
     * @bodyParam status string required Trạng thái: active, inactive. Example: active
     * @bodyParam parent_id integer ID danh mục cha (để trống nếu là gốc). Example: 1
     * @bodyParam sort_order integer Thứ tự ưu tiên. Example: 1
     *
     * @apiResource App\Modules\Category\Resources\CategoryResource status=201
     * @apiResourceModel App\Modules\Category\Models\Category
     * @apiResourceAdditional success=true message="Danh mục đã được tạo thành công!"
     */
    public function store(StoreCategoryRequest $request)
    {
        $category = $this->categoryService->store($request->validated());

        return $this->successResource(new CategoryResource($category), 'Danh mục đã được tạo thành công!', 201);
    }

    /**
     * Cập nhật danh mục
     *
     * @urlParam category integer required ID danh mục. Example: 1
     *
     * @bodyParam title string Tiêu đề cập nhật.
     * @bodyParam status string Trạng thái mới.
     * @bodyParam parent_id integer ID cha mới.
     *
     * @apiResource App\Modules\Category\Resources\CategoryResource
     * @apiResourceModel App\Modules\Category\Models\Category
     * @apiResourceAdditional success=true message="Danh mục đã được cập nhật!"
     */
    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $result = $this->categoryService->update($category, $request->validated());
        if (! $result['ok']) {
            return $this->error($result['message'], $result['code'], null, $result['error_code']);
        }

        return $this->successResource(new CategoryResource($result['category']), 'Danh mục đã được cập nhật!');
    }

    /**
     * Xóa danh mục
     *
     * @urlParam category integer required ID danh mục cần xóa. Example: 1
     *
     * @response 200 {"success": true, "message": "Danh mục đã được xóa!"}
     */
    public function destroy(Category $category)
    {
        $this->categoryService->destroy($category);

        return $this->success(null, 'Danh mục đã được xóa!');
    }

    /**
     * Xóa hàng loạt danh mục
     *
     * @bodyParam ids array required Danh sách ID cần xóa. Example: [1, 2, 3]
     *
     * @response 200 {"success": true, "message": "Đã xóa thành công các danh mục được chọn!"}
     */
    public function bulkDestroy(BulkDestroyCategoryRequest $request)
    {
        $this->categoryService->bulkDestroy($request->ids);

        return $this->success(null, 'Đã xóa thành công các danh mục được chọn!');
    }

    /**
     * Cập nhật trạng thái hàng loạt
     *
     * @bodyParam ids array required Danh sách ID. Example: [1, 2, 3]
     * @bodyParam status string required Trạng thái mới: active, inactive. Example: inactive
     *
     * @response 200 {"success": true, "message": "Cập nhật trạng thái thành công các danh mục được chọn!"}
     */
    public function bulkUpdateStatus(BulkUpdateStatusCategoryRequest $request)
    {
        $this->categoryService->bulkUpdateStatus($request->ids, $request->status);

        return $this->success(null, 'Cập nhật trạng thái thành công các danh mục được chọn!');
    }

    /**
     * Xuất danh sách danh mục (Excel)
     *
     * Xuất toàn bộ hoặc theo bộ lọc ra file Excel.
     *
     * @queryParam search string Tìm kiếm.
     * @queryParam status string Trạng thái.
     */
    public function export(FilterRequest $request)
    {
        return $this->categoryService->export($request->all());
    }

    /**
     * Nhập danh sách danh mục
     *
     * @bodyParam file file required File Excel (.xlsx, .xls, .csv).
     *
     * @response 200 {"success": true, "message": "Import danh mục thành công."}
     */
    public function import(ImportCategoryRequest $request)
    {
        $this->categoryService->import($request->file('file'));

        return $this->success(null, 'Import danh mục thành công.');
    }

    /**
     * Thay đổi trạng thái danh mục
     *
     * @urlParam category integer required ID.
     * @bodyParam status string required Trạng thái mới. Example: active
     *
     * @apiResource App\Modules\Category\Resources\CategoryResource
     * @apiResourceModel App\Modules\Category\Models\Category
     * @apiResourceAdditional success=true message="Cập nhật trạng thái thành công!"
     */
    public function changeStatus(ChangeStatusCategoryRequest $request, Category $category)
    {
        $result = $this->categoryService->update($category, ['status' => $request->status]);

        return $this->successResource(new CategoryResource($result['category']), 'Cập nhật trạng thái thành công!');
    }
}
