<?php


namespace App\Modules\Jobs\Enums;
/**
 * Trạng thái công việc.
 */
enum JobStatusEnum: string
{
    case Draft = 'draft';
    case Published = 'published';
    case Archived = 'archived';
    case Completed = 'completed';


    /** Danh sách giá trị để validate. */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /** Rule validation: in:draft,published,archived */
    public static function rule(): string
    {
        return 'in:'.implode(',', self::values());
    }

    /** Nhãn tiếng Việt. */
    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Bản nháp',
            self::Published => 'Đã xuất bản',
            self::Archived => 'Lưu trữ',
            self::Completed => 'Hoàn thành', // 2. THÊM NHÃN TIẾNG VIỆT
        };
    }
}
