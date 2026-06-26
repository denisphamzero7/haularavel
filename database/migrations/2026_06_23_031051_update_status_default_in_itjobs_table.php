<?php

use App\Modules\Jobs\Enums\JobStatusEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       // 1. Cập nhật các data cũ từ 'active' về 'draft' (nếu có) trước khi đổi default
        DB::table('itjobs')
            ->where('status', 'active')
            ->update(['status' => JobStatusEnum::Draft->value]);

        // 2. Thay đổi giá trị mặc định của cột status thành 'draft'
        Schema::table('itjobs', function (Blueprint $table) {
            $table->string('status', 20)->default(JobStatusEnum::Draft->value)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
       Schema::table('itjobs', function (Blueprint $table) {
            // Khôi phục lại trạng thái ban đầu nếu rollback
            $table->string('status', 20)->default('active')->change();
        });
    }
};
