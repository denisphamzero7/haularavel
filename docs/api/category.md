# API Danh mục (Category)

Quản lý danh mục bài viết và tài liệu phân cấp theo cấu trúc cây `parent_id`: thống kê, danh sách, CRUD, xóa hàng loạt, xuất/nhập Excel.

**Base path:** `/api/category`

---


## Danh sách danh mục (Phân trang)

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/category` |
| **Query** | `search`, `status`, `sort_by` (id \| title \| sort_order \| created_at), `sort_order` (asc \| desc), `limit`. |
| **Response** | Paginated collection (CategoryResource), mỗi item có `creator`, `editor`, `parent`. |

---

## Chi tiết danh mục

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/category/{id}` |
| **UrlParam** | `id` — ID danh mục. |
| **Response** | Object danh mục (CategoryResource), kèm `parent`. |

---

## Tạo danh mục

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/category` |
| **Body** | `title` (required), `description` (optional), `status` (optional), `parent_id` (optional), `sort_order` (optional). |
| **Response** | 201, object danh mục + `"message": "Danh mục đã được tạo thành công!"`. |

---

## Cập nhật danh mục

| | |
|---|---|
| **Method** | PUT / PATCH |
| **Path** | `/api/category/{id}` |
| **Body** | `title`, `description`, `status`, `parent_id`, `sort_order`. |
| **Response** | Object danh mục + `"message": "Danh mục đã được cập nhật!"`. |

---

## Xóa danh mục

| | |
|---|---|
| **Method** | DELETE |
| **Path** | `/api/category/{id}` |
| **Response** | `{ "success": true, "message": "Danh mục đã được xóa!" }`. |

---

## Xóa hàng loạt

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/category/bulk-delete` |
| **Body** | `ids` (array) — danh sách ID danh mục. |
| **Response** | `{ "success": true, "message": "Đã xóa thành công các danh mục được chọn!" }`. |

---

## Xuất Excel

| | |
|---|---|
| **Method** | GET |
| **Path** | `/api/category/export` |
| **Query** | Cùng bộ lọc với index: `search`, `status`, `sort_by`, `sort_order`. |
| **Response** | File `categories.xlsx`. |

---

## Nhập Excel

| | |
|---|---|
| **Method** | POST |
| **Path** | `/api/category/import` |
| **Body** | `file` (required) — xlsx, xls, csv. Cột: title, description, status. |
| **Response** | `{ "success": true, "message": "Import danh mục bài viết thành công." }`. |

---

## Response mẫu (CategoryResource)

```json
{
  "id": 1,
  "title": "Tin công nghệ",
  "description": "Các bài viết về AI và phần mềm",
  "status": "active",
  "parent_id": null,
  "sort_order": 1,
  "created_by": "Quản trị viên",
  "updated_by": "Quản trị viên",
  "created_at": "01/05/2026 15:30:00",
  "updated_at": "01/05/2026 16:00:00"
}
```
