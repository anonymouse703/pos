<?php

namespace App\Cache\File;

use App\Cache\CacheBase;
use App\Models\File as Model;

/**
 * @method Model|null fetch()
 * @method Model fetchOrFail()
 */
 
class FileById extends CacheBase
{
    public function __construct(protected int $id)
    {
        parent::__construct("files.{$id}", now()->addHour());
    }

    protected function cacheMiss()
    {
        return Model::find($this->id);
    }

    protected function errorModelName(): string
    {
        return "File";
    }

    protected function errorModelId()
    {
        return $this->id;
    }
}
