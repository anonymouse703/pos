<?php

namespace App\Repositories\Contracts;

use App\Models\Customer;

/**
 * @method Customer|null find(mixed $id)
 * @method Customer|null first()
 */
interface CustomerRepositoryInterface extends RepositoryInterface
{
	//define set of methods that CustomerRepositoryInterface Repository must implement
	public function filterByKeyword(?string $keyword = null): self;
	public function filterByDate($startDate, $endDate) :self;
}
