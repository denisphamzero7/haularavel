# Phân tích và Triển khai Module Danh mục (Category)

**Ngày tạo:** 2026-05-01  
**Mục đích:** Ghi lại cấu trúc chuẩn hóa cho module Danh mục dùng chung cho toàn hệ thống (bài viết, tài liệu), hỗ trợ đa tổ chức và đồng bộ quy chuẩn với các module khác.

---

## 1. Tổng quan cấu trúc

Module Category được thiết kế để thay thế cho cách tiếp cận cũ (chia nhỏ theo từng loại module). Nó đóng vai trò là module dùng chung, phân biệt theo `organization_id`.

### 1.1 Cơ sở dữ liệu (Database)
- **Bảng:** `categories`
- **Các cột chính:**
    - `id`: Khóa chính.
    - `organization_id`: ID tổ chức (đa nhiệm).
    - `title`: Tiêu đề danh mục.
    - `description`: Mô tả.
    - `status`: Trạng thái (active, inactive).
    - `sort_order`: Thứ tự hiển thị.
    - `parent_id`: ID cha (hỗ trợ phân cấp).
    - `created_by`, `updated_by`: Theo dõi người dùng thực hiện.

### 1.2 Quy chuẩn đặt tên (Naming Convention)
Để đồng bộ với các module khác như `Post` hay `Document`, các lớp hỗ trợ Excel được đặt tên ở số nhiều:
- `CategoriesExport`: Xuất dữ liệu Excel.
- `CategoriesImport`: Nhập dữ liệu Excel.

---

## 2. Các tính năng chính

### 2.1 Quản lý đa tổ chức (Multi-tenancy)
Hệ thống tự động nhận diện tổ chức hiện tại thông qua:
1. Header `X-Organization-Id` (ưu tiên).
2. Thông tin người dùng đang đăng nhập (`current_organization_id`).
3. Hàm bổ trợ `getPermissionsTeamId()`.

### 2.2 Import/Export Excel
- **Export:** Cho phép xuất danh sách danh mục kèm theo thông tin người tạo và người cập nhật bằng Eager Loading.
- **Import:** Hỗ trợ nhập file Excel, tự động gán `organization_id` cho các danh mục mới để đảm bảo bảo mật dữ liệu.

### 2.3 Tự động hóa (Automation)
Sử dụng Eloquent Model Booted để tự động gán:
- `created_by` và `updated_by` khi thực hiện các thao tác tạo mới hoặc cập nhật.

---

## 3. Hướng dẫn sử dụng API

Tài liệu API chi tiết được quản lý tại:
- [Tài liệu Scribe (Giao diện web)](http://localhost:8000/docs)
- [Tài liệu Markdown (docs/api/category.md)](../api/category.md)
