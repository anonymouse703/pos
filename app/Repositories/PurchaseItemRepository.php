<?php

namespace App\Repositories;

use App\Models\PurchaseItem;
use App\Repositories\Contracts\PurchaseItemRepositoryInterface;

class PurchaseItemRepository extends BaseRepository implements PurchaseItemRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(app(PurchaseItem::class));
    }
}
