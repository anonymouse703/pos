<?php

namespace App\Repositories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Contracts\CategoryRepositoryInterface;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(app(Category::class));
    }

     public function filterByKeyword(?string $keyword = null): self
    {
        return $this->filter(static function (Builder $builder) use($keyword){
            $builder->when($keyword, fn($q) =>
                    $q->where(function($q) use ($keyword) {
                        $q->where('name', 'LIKE', "%{$keyword}%");
                    })
                );
        });
    }

    public function filterByDate($startDate, $endDate): self
    {
        return $this->filter(function (Builder $builder) use ($startDate, $endDate) {

            if (!empty($startDate)) {
                $builder->where('created_at', '>=', $startDate);
            }

            if (!empty($endDate)) {
                $builder->where('created_at', '<=', $endDate);
            }
        });
    }
}
