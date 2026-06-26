<?php

namespace App\Console\Commands;

use App\Modules\Jobs\Events\JobUpdatedEvent;
use App\Modules\Jobs\Models\ITJob as Task;
use App\Modules\Jobs\Enums\JobStatusEnum; // Khuyến khích dùng Enum nếu có
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendTaskDueReminders extends Command
{
    protected $signature = 'tasks:send-reminders';
    protected $description = 'Quét và gửi thông báo nhắc nhở công việc sắp tới hạn';

    public function handle()
    {
        // THÊM DÒNG NÀY ĐỂ DEBUG
        \Illuminate\Support\Facades\Log::info('🤖 Scheduler nhắc việc vừa chạy lúc: ' . now());
        $now = Carbon::now();

        // Sử dụng whereBetween để code gọn và truy vấn SQL tối ưu hơn
        $upcomingTasks = Task::where('status', '!=', 'completed') // Nếu có JobStatusEnum::Completed->value thì nên dùng Enum
            ->where('is_notified', false)
            ->whereBetween('due_at', [$now, $now->copy()->addMinutes(15)])
            ->where('is_notified', false) //
            // THÊM DÒNG NÀY: Bỏ qua những công việc đã hoàn thành hoặc lưu trữ
            ->whereNotIn('status', [
                JobStatusEnum::Completed->value,
                JobStatusEnum::Archived->value
            ])
            ->get();

        if ($upcomingTasks->isEmpty()) {
            $this->info('Không có công việc nào sắp tới hạn cần nhắc nhở.');
            return;
        }

        foreach ($upcomingTasks as $task) {
            // Phát thông báo khẩn cấp Realtime tới Client
            broadcast(new JobUpdatedEvent('task-reminder', [
                'id' => $task->id,
                'title' => $task->title,
                'message' => "Công việc [{$task->title}] sắp đến hạn lúc " . $task->due_at->format('H:i')
            ]));

            // Đánh dấu đã thông báo
            $task->update(['is_notified' => true]);
        }

        $this->info('Đã gửi thông báo thành công cho ' . $upcomingTasks->count() . ' công việc.');
    }
}
