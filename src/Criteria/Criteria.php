<?php

namespace Davesweb\Repositories\Criteria;

use Davesweb\Repositories\Repository;
use Illuminate\Database\Eloquent\Builder;

interface Criteria
{
    /**
     * @param Builder    $model
     * @param Repository $repository
     *
     * @return Criteria
     */
    public function apply(Builder $model, Repository $repository): self;
}
