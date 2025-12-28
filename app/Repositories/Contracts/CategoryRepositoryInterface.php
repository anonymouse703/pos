<?php

namespace App\Repositories\Contracts;

use App\Models\Category;

/**
 * @method Category|null find(mixed $id)
 * @method Category|null first()
 */
interface CategoryRepositoryInterface extends RepositoryInterface
{
	//define set of methods that CategoryRepositoryInterface Repository must implement
	public function filterByKeyword(?string $keyword = null): self;
	public function filterByDate($startDate, $endDate) :self;

}
