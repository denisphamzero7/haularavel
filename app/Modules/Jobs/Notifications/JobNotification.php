<?php

namespace App\Modules\Jobs\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class JobNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $action,
        public array $jobData
    ) {}

    /**
     * Khai báo các kênh thông báo sẽ kích hoạt
     */
    public function via(object $notifiable): array
    {
        // Vừa gửi mail, vừa bắn realtime qua Reverb
        return ['mail', 'broadcast'];
    }

    /**
     * Cấu hình nội dung EMAIL (Thay thế hoàn toàn file Mailable trước đó)
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = match ($this->action) {
            'job-created' => '🚀 Công việc mới được phân công',
            'job-updated' => '✏️ Công việc đã cập nhật',
            'job-deleted'=>'Công việc đã được xóa khỏi danh sách công việc',
            'job-bulk-deleted'=>' Các công việc đã được xóa',
            'job-bulk-status-updated'=>'Trạng thái của các công việc đã được thay đổi',
            default => '🔔 Thông báo hệ thống'
        };

        return (new MailMessage)
            ->subject($subject)
            ->greeting('Chào bạn,')
            ->line('Hệ thống có thông báo mới về công việc của bạn:')
            ->line('• Tiêu đề: ' . ($this->jobData['title'] ?? ''))
            ->line('• Trạng thái: ' . ($this->jobData['status'] ?? ''))
            ->action('Xem chi tiết công việc', url('/jobs/' . ($this->jobData['id'] ?? '')))
            ->line('Cảm ơn bạn đã sử dụng hệ thống!');
    }

    /**
     * Cấu hình dữ liệu REALTIME qua Reverb (Thay thế file Event trước đó)
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'action' => $this->action,
            'data' => $this->jobData
        ]);
    }

    /**
     * Tùy chọn: Định nghĩa tên sự kiện để Frontend lắng nghe (tương đương broadcastAs)
     */
    public function broadcastType(): string
    {
        return 'JobEvent';
    }
}
