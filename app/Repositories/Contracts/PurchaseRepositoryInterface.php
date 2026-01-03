<?php

namespace App\Repositories\Contracts;

use App\Models\Purchase;

/**
 * @method Purchase|null find(mixed $id)
 * @method Purchase|null first()
 */
interface PurchaseRepositoryInterface extends RepositoryInterface
{
	//define set of methods that PurchaseRepositoryInterface Repository must implement
	public function filterByKeyword(?string $keyword = null): self;
	public function filterByDate($startDate, $endDate) :self;
}
