<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'credit_limit',
        'opening_balance',
        'profile_image_id',
    ];

    public function profilePhoto() : BelongsTo
    {
        return $this->belongsTo(File::class, 'profile_image_id');
    }
}
