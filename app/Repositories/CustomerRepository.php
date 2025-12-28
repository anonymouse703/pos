<?php

namespace App\Repositories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Builder;
use App\Repositories\Contracts\CustomerRepositoryInterface;

class CustomerRepository extends BaseRepository implements CustomerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(app(Customer::class));
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
