<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Criteria;

use ActivismeBE\DatabaseLayering\Repositories\Contracts\RepositoryInterface;

abstract class Criteria 
{
    /**
     * @param $model
     * @param RepositoryInterface $repository
     * @return mixed
     */
    public abstract function apply($model, RepositoryInterface $repository);
}