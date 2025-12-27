<?php

namespace App\Services\FileUploader\Uploaders;

use App\Enums\File\Disk;
use App\Enums\File\Visibility;
use App\Models\User;
use App\Services\FileUploader\Facades\FileUploader;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;

class RawFileUploader
{
    protected ?User $user;
    protected UploadedFile $uploadedFile;
    protected string $storagePath = 'raw';
    protected bool $generateThumbnail = false;
    protected string $disk;

    protected ?int $episode;
    protected Visibility $visibility = Visibility::Private;

    public function __construct(UploadedFile $uploadedFile, User $user)
    {
        $this->uploadedFile = $uploadedFile;
        $this->user = $user;
        $this->disk = config('filesystems.default');
    }

    public static function uploadRaw(
        UploadedFile $uploadedFile,
        User $user,
        Disk $disk,
        string $path = 'videos',
        ?int $episode = null,
    ) : int
    {
        $uploader = new self($uploadedFile, $user);
        $uploader->storagePath = $path;
        if(in_array(config('filesystems.default'), Disk::s3Disks(true))) {
            $uploader->disk = $disk->value;
        }
        $uploader->episode = $episode;

        return $uploader->upload();
    }

    public static function uploadAudio(
        UploadedFile $uploadedFile,
        User $user,
        Disk $disk,
        string $path = 'audios',
        ?int $episode = null,
    ) : int
    {
        $uploader = new self($uploadedFile, $user);
        $uploader->storagePath = $path;
        if(in_array(config('filesystems.default'), Disk::s3Disks(true))) {
            $uploader->disk = $disk->value;
        }
        $uploader->episode = $episode;
        $uploader->visibility = Visibility::Public;

        return $uploader->upload();
    }

    protected function upload(): int
    {
        $filename = Str::random(8);

        if ($this->episode) {
            $filename = str_pad($this->episode, 3, "0", STR_PAD_LEFT);
        }

        $file = FileUploader::upload($this->uploadedFile)
            ->uploader($this->user)
            ->storagePath($this->storagePath)
            ->disk($this->disk)
            ->fileName($filename)
            ->setVisibility($this->visibility)
            ->saveUsingFilename(!!$this->episode);

        if ($this->generateThumbnail) {
            $file->generateThumbnail(300, 300);
        }

        return $file->getFile()->id;
    }

}
