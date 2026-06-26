<?php


namespace App\Modules\Jobs\Models;

use App\Modules\Core\Models\User;
use App\Modules\Core\Enums\StatusEnum;
use App\Modules\Jobs\Enums\JobStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


class ITJob extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected static function newFactory()
    {
        return \Database\Factories\JobFactory::new();
    }

    protected $table = 'itjobs';

    protected $fillable = [
        'title',
        'description',
        // 'company_name',
        'status',
        'organization_id',
        'created_by',
        'updated_by',
        'due_at',       // THÊM MỚI
        'is_notified',  // THÊM MỚI
    ];

    protected $casts = [
        'organization_id' => 'integer',
        'status'=>JobStatusEnum::class,
        'due_at' => 'datetime', // THÊM MỚI: Ép kiểu thành Carbon Object để Command không bị lỗi định dạng
        'is_notified' => 'boolean', // THÊM MỚI
    ];

    protected static function booted()
    {
       // Khi tạo mới: Gán cả người tạo và người sửa bằng ID user hiện tại đang đăng nhập
        static::creating(function ($job) {
            if (auth()->check()) {
                $job->created_by = auth()->id();
                $job->updated_by = auth()->id();
            }
        });

        // Khi cập nhật: Chỉ cập nhật lại ID người sửa
        static::updating(function ($job) {
            if (auth()->check()) {
                $job->updated_by = auth()->id();
            }
        });
    }

    /**
     * Scope filter danh sách công việc.
     */
  /**
     * Scope filter và sort danh sách công việc.
     */
    public function scopeFilter($query, array $filters = [])
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            // Cần bọc where/orWhere trong 1 function để tạo ngoặc ( ) trong SQL
            // Tránh lỗi: WHERE status = 'draft' AND title LIKE '%A%' OR description LIKE '%A%'
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        })->when($filters['status'] ?? null, function ($query, $status) {
            $query->where('status', $status);
        })->when($filters['from_date'] ?? null, function ($query, $fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        })->when($filters['to_date'] ?? null, function ($query, $toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        })->when($filters['created_by'] ?? null, function ($query, $createdBy) {
            $query->where('created_by', $createdBy);
        })->when($filters['sort_by'] ?? 'id', function ($query, $sortBy) use ($filters) {
            // Tích hợp luôn logic sort vào đây
            $allowed = ['id', 'title', 'status', 'created_at', 'updated_at'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'id';

            $sortOrder = in_array($filters['sort_order'] ?? 'desc', ['asc', 'desc']) ? $filters['sort_order'] : 'desc';

            $query->orderBy($column, $sortOrder);
        });
    }

   

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
