<?php

namespace ActivismeBE\DatabaseLayering\Tests\Resources\Repositories;

use ActivismeBE\DatabaseLayering\Repositories\Eloquent\Repository;
use ActivismeBE\DatabaseLayering\Tests\Resources\Models\User;

class UserRepository extends Repository
{
    public function model()
    {
        return User::class;
    }
}
