<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'name_ur',
        'type',
        'short_description',
        'description',
        'description_ur',
        'benefits',
        'how_to_use',
        'images',
        'status',
        'is_featured',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'benefits' => 'array',
            'how_to_use' => 'array',
            'images' => 'array',
            'is_featured' => 'boolean',
        ];
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function activeVariants(): HasMany
    {
        return $this->variants()->where('is_active', true);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}
