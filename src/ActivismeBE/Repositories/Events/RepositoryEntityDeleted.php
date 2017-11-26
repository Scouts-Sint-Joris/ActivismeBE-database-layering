<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Events;

/**
 * Class RepositoryEntityDeleted
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Events
 */
class RepositoryEntityDeleted extends RepositoryEventBase
{
    /**
     * The name for the action.
     *
     * @return string
     */
    protected $action = 'deleted';
}
