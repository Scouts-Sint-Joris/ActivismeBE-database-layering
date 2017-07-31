<?php

namespace ActivismeBE\DatabaseLayering\Tests\Repositories;

use \Mockery as m;

class RepositoryTest extends \Orchestra\Testbench\TestCase
{
    protected $mock;
    protected $repository;
    
    public function setUp() 
    {
        parent::setUp();
        $this->mock = m::mock('Illuminate\Database\Eloquent\Model');
    }

    protected function getPackageProviders($app)
    {
        return ['ActivismeBE\DatabaseLayering\Repositories\Providers\RepositoryProvider'];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }
}