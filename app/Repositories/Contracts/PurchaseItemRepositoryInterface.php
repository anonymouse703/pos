<?php

namespace App\Repositories\Contracts;

use App\Models\PurchaseItem;

/**
 * @method PurchaseItem|null find(mixed $id)
 * @method PurchaseItem|null first()
 */
interface PurchaseItemRepositoryInterface extends RepositoryInterface
{
	//define set of methods that PurchaseItemRepositoryInterface Repository must implement
}
