<?php

namespace App\Services\FileUpload;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\File;

class FileUploadService
{
    public function upload(
        UploadedFile $file,
        int $uploaderId,
        string $directory = 'uploads/profile-images',
        string $disk = 'public'
    ): File {
        $path = $file->store($directory, $disk);

        return File::create([
            'uploader_id'    => $uploaderId,
            'path'           => $path,
            'disk'           => $disk,
            'name'           => $file->getClientOriginalName(),
            'size'           => $file->getSize(),
            'type'           => $file->getMimeType(),
            'extension'      => $file->getClientOriginalExtension(),
            'thumbnail_path' => null, // Thumbnail generation can be handled separately
        ]);
    }
}
