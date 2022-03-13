<?php

namespace App\Repositories\Eloquent\Criteria;

use App\Repositories\Criteria\ICriterion;


class WithTrash implements ICriterion
{
    public function apply($model)
    {
        return $model->withTrashed();
    }
}
