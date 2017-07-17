<?php 
namespace Bosnadev\Repositories\Criteria;

use ActivismeBE\DatabaseLayering\Contracts\RepositoryInterface as Repository;

abstract class Criteria 
{
    /**
     * @param $model
     * @param Repository $repository
     * @return mixed
     */
    public abstract function apply($model, Repository $repository);
}