<?php

namespace ActivismeBE\DatabaseLayering\Tests\Resources\Repositories;

use ActivismeBE\DatabaseLayering\Tests\Resources\Models\User;
use ActivismeBE\DatabaseLayering\Repositories\{
    Contracts\RepositoryInterface,
    Eloquent\Repository
};


class UserRepository extends Repository
{
    public function model()
    {
        return User::class;
    }
}