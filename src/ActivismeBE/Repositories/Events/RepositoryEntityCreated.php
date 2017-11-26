<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Events;

/**
 * Class RepositoryEntityCreated
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Events
 */
class RepositoryEntityCreated extends RepositoryEventBase
{
    /**
     * The name for the action.
     *
     * @var string
     */
    protected $action = 'created';
}
