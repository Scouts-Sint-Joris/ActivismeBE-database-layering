<?php 

namespace ActivismeBE\DatabaseLayering\Tests\Repositories;

class FindorFailTest extends BaseTest
{
    public function testFindOrFailSuc()
    {
        $call = $this->repository->findOrFail(1);

        $this->assertEquals('firstname', $call->first_name);
        $this->assertEquals('lastname', $call->last_name);
        $this->assertEquals('email@example.tld', $call->email);
        $this->assertEquals('secret', $call->password);
    }

    public function testFindOrFailErr()
    {
    }
}
