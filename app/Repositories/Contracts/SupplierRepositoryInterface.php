<?php

namespace App\Repositories\Contracts;

use App\Models\Supplier;

/**
 * @method Supplier|null find(mixed $id)
 * @method Supplier|null first()
 */
interface SupplierRepositoryInterface extends RepositoryInterface
{
	//define set of methods that SupplierRepositoryInterface Repository must implement
	public function filterByKeyword(?string $keyword = null): self;
	public function filterByDate($startDate, $endDate) :self;
}
