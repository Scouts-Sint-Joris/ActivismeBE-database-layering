<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Events;

/**
 * Class RepositoryEntityUpdated
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Events
 */
class RepositoryEntityUpdated extends RepositoryEventBase
{
    /**
     * The name for the action.
     *
     * @var string
     */
    protected $action = 'updated';
}
