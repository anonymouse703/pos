<?php

namespace App\Cache\File;

use App\Cache\CacheBase;
use App\Models\File as Model;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @method Model|null fetch()
 * @method Model fetchOrFail()
 */
 
class TemporaryUrl extends CacheBase
{
    public function __construct(protected string $path, protected string $disk, Carbon $ttl)
    {
        parent::__construct("files.{$path}", $ttl);
    }

    protected function cacheMiss()
    {
        return Storage::disk($this->disk)->temporaryUrl($this->path, $this->ttl);
    }

    protected function errorModelName(): string
    {
        return "File";
    }

    protected function errorModelId()
    {
        return $this->path;
    }
}
