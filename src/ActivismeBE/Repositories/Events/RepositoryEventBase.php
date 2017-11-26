<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Events;

use ActivismeBE\DatabaseLayering\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Class RepositoryEventBase
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Events
 */
abstract class RepositoryEventBase
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var string
     */
    protected $action;

    /**
     * RepositoryEventBase constructor.
     *
     * @param   RepositoryInterface   $repository
     * @param   Model                 $model
     * @return  void
     */
    public function __construct(RepositoryInterface $repository, Model $model)
    {
        $this->repository = $repository;
        $this->model      = $model;
    }

    /**
     * Get the model from the repository.
     *
     * @return Model
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @return RepositoryInterface
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }
}
