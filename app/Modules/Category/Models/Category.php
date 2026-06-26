<?php

namespace App\Modules\Category\Models;

use App\Modules\Core\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Category extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected static function newFactory()
    {
        return \Database\Factories\Modules\Category\Models\CategoryFactory::new();
    }

    protected $fillable = [
        'organization_id',
        'title',
        'description',
        'status',
        'sort_order',
        'parent_id',
        'created_by',
        'updated_by',
    ];
   protected static function booted()
    {
        static::creating(fn (Category $model) => $model->created_by = $model->updated_by = auth()->id());
        static::updating(fn (Category $model) => $model->updated_by = auth()->id());
    }

    public function organization()
    {
        return $this->belongsTo(\App\Modules\Core\Models\Organization::class);
    } 

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function editor()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order');
    }
    // protected $casts = [
    //     'view_count' => 'integer',
    //     'organization_id' => 'integer',
    // ];

  


    // /** Danh mục của bài viết (quan hệ nhiều-nhiều qua bảng pivot). */
    // public function categories()
    // {
    //     return $this->belongsToMany(PostCategory::class, 'post_post_category', 'post_id', 'post_category_id')
    //         ->withTimestamps();
    // }

    // public function attachments()
    // {
    //     return $this->media()->where('collection_name', 'post-attachments')->orderBy('order_column');
    // }

    // public function registerMediaCollections(): void
    // {
    //     $this->addMediaCollection('post-attachments');
    // }

    public function registerMediaConversions(?Media $media = null): void {}

   public function scopeFilter($query, array $filters)
    {
        $query->when($filters['search'] ?? null, function ($query, $search) {
            // Tìm kiếm theo title
            $query->where('title', 'like', '%'.$search.'%');
        })->when($filters['status'] ?? null, function ($query, $status) {
            // Lọc theo trạng thái
            $query->where('status', $status);
        })->when($filters['from_date'] ?? null, function ($query, $fromDate) {
            $query->whereDate('created_at', '>=', $fromDate);
        })->when($filters['to_date'] ?? null, function ($query, $toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        })->when($filters['sort_by'] ?? 'created_at', function ($query, $sortBy) use ($filters) {
            $allowed = ['id', 'title', 'created_at', 'status'];
            $column = in_array($sortBy, $allowed) ? $sortBy : 'created_at';
            $query->orderBy($column, $filters['sort_order'] ?? 'desc');
        });
    }
    
}
