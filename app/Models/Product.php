<?php

namespace App\Models;

use App\Enums\ProductStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'code',
        'name',
        'description',
        'brand',
        'origin',
        'status',
        'price_factory_cash',
        'price_factory_installment',
        'factory_installment_terms',
        'price_baloochi_cash',
        'price_baloochi_installment',
        'baloochi_installment_terms',
        'image_path',
        'image_url',
        'pdf_path',
        'pdf_url',
        'extra_info',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'status' => ProductStatus::class,
            'price_factory_cash' => 'integer',
            'price_factory_installment' => 'integer',
            'price_baloochi_cash' => 'integer',
            'price_baloochi_installment' => 'integer',
            'sort_order' => 'integer',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);
        if ($term === '') {
            return $query;
        }

        $like = '%'.str_replace(['\\', '%', '_'], ['\\\\', '\\%', '\\_'], $term).'%';

        return $query->where(function (Builder $q) use ($like) {
            $q->where('name', 'like', $like)
                ->orWhere('code', 'like', $like)
                ->orWhere('brand', 'like', $like);
        });
    }

    /** آدرس نهایی تصویر: فایل آپلودی اولویت دارد، سپس لینک خارجی */
    protected function imageSrc(): Attribute
    {
        return Attribute::get(fn () => $this->image_path
            ? Storage::disk('public')->url($this->image_path)
            : ($this->image_url ?: null));
    }

    protected function pdfSrc(): Attribute
    {
        return Attribute::get(fn () => $this->pdf_path
            ? Storage::disk('public')->url($this->pdf_path)
            : ($this->pdf_url ?: null));
    }

    /** قیمت نمایشی در لیست ادمین */
    protected function displayPrice(): Attribute
    {
        return Attribute::get(fn () => $this->price_baloochi_cash ?: $this->price_factory_cash);
    }
}
