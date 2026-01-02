<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class File extends Model
{
    protected $fillable = [
        'uploader_id',
        'path',
        'disk',
        'name',
        'size',
        'type',
        'extension',
        'thumbnail_path',
    ];

    public function getFullUrlAttribute(): ?string
    {
        if (!$this->path || !$this->disk) {
            return null;
        }

        return Storage::disk($this->disk)->url($this->path);
    }
}
