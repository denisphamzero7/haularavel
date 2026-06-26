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
        Schema::table('itjobs', function (Blueprint $table) {
            // Thêm cột due_at (kiểu datetime, cho phép null vì các job cũ có thể không có hạn)
            $table->dateTime('due_at')->nullable()->after('updated_by');

            // Thêm cột is_notified (kiểu boolean, mặc định là false - chưa thông báo)
            $table->boolean('is_notified')->default(false)->after('due_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('itjobs', function (Blueprint $table) {
            $table->dropColumn(['due_at', 'is_notified']);
        });
    }
};
