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
        Schema::create('log_activities', function (Blueprint $blueprint) {
            $blueprint->id();
            $blueprint->string('description')->nullable();
            $blueprint->string('user_type')->nullable();
            $blueprint->unsignedBigInteger('user_id')->nullable();
            $blueprint->unsignedBigInteger('organization_id')->nullable();
            $blueprint->string('route')->nullable();
            $blueprint->string('method_type')->nullable();
            $blueprint->integer('status_code')->nullable();
            $blueprint->string('ip_address')->nullable();
            $blueprint->string('country')->nullable();
            $blueprint->text('user_agent')->nullable();
            $blueprint->json('request_data')->nullable();
            $blueprint->timestamps();

            $blueprint->index(['user_id', 'user_type']);
            $blueprint->index('organization_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('log_activities');
    }
};
