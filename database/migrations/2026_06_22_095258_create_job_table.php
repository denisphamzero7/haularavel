<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('itjobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();

            // Thay enum bằng string(20) để dễ mở rộng trạng thái sau này
            $table->string('status', 20)->default('active');

            // Cú pháp khóa ngoại rút gọn + Thêm index để tối ưu truy vấn theo tổ chức
            $table->foreignId('organization_id')
                  ->nullable()
                  ->index()
                  ->constrained('organizations')
                  ->nullOnDelete();

            // Khóa ngoại cho người tạo và người cập nhật
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->foreignId('updated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('itjobs');
    }
};
