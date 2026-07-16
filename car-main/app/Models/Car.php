<?php

namespace App\Models;

use App\Enums\CarApprovalStatus;
use App\Enums\CarCondition;
use App\Enums\CarStatus;
use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Models\Concerns\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Car extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CarFactory> */
    use HasFactory, SoftDeletes, HasSlug, InteractsWithMedia, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'owner_id',
        'brand_id',
        'car_model_id',
        'title',
        'slug',
        'description',
        'specifications',
        'year',
        'price',
        'min_installment',
        'mileage',
        'condition',
        'transmission',
        'fuel_type',
        'grade',
        'color',
        'status',
        'approval_status',
        'rejection_reason',
        'featured',
        'is_active',
        'published_at',
        'view_count',
        'meta_title',
        'meta_description',
    ];

    /**
     * The model's default values for attributes.
     *
     * @var array<string, mixed>
     */
    protected $attributes = [
        'mileage' => 0,
        'view_count' => 0,
        'status' => 'available',
        'approval_status' => 'pending',
        'featured' => false,
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'price' => 'decimal:2',
            'min_installment' => 'decimal:2',
            'mileage' => 'integer',
            'view_count' => 'integer',
            'specifications' => 'array',
            'featured' => 'boolean',
            'is_active' => 'boolean',
            'published_at' => 'datetime',
            'condition' => CarCondition::class,
            'transmission' => TransmissionType::class,
            'fuel_type' => FuelType::class,
            'status' => CarStatus::class,
            'approval_status' => CarApprovalStatus::class,
        ];
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function carModel(): BelongsTo
    {
        return $this->belongsTo(CarModel::class);
    }

    // Keeping these for backward compatibility during migration, but Spatie Media Library is preferred now
    public function images(): HasMany
    {
        return $this->hasMany(CarImage::class)->orderBy('sort_order');
    }

    public function primaryImage(): HasOne
    {
        return $this->hasOne(CarImage::class)->where('is_primary', true);
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function purchaseRequests(): HasMany
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('approval_status', CarApprovalStatus::Approved);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', CarStatus::Available);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('featured', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereNotNull('published_at')->where('published_at', '<=', now());
    }

    /**
     * Override getSlugSourceField from HasSlug trait to use 'title' instead of 'name'
     */
    protected function getSlugSourceField(): string
    {
        return 'title';
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('car_gallery');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit('contain', 200, 200);

        $this->addMediaConversion('medium')
            ->fit('contain', 800, 600);

        $this->addMediaConversion('large')
            ->fit('contain', 1920, 1080);
    }
}
