<?php

namespace App\Models\Traits;

use Illuminate\Support\Str;

trait GeneratesSku
{
    protected static function bootGeneratesSku(): void
    {
        static::creating(function ($model) {
            if (empty($model->sku)) {
                $model->sku = $model->generateUniqueSku();
            }
        });
    }

    /**
     * Generate a unique SKU
     */
    protected function generateUniqueSku(
        string $prefix = 'PRD',
        int $length = 6,
        string $column = 'sku'
    ): string {
        do {
            $sku = $prefix . '-' . strtoupper(Str::random($length));
        } while (static::where($column, $sku)->exists());

        return $sku;
    }
}
