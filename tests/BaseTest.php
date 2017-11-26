<?php

namespace ActivismeBE\DatabaseLayering\Tests\Repositories;

use \Illuminate\Database\Schema\Blueprint;
use \Mockery as m;
use ActivismeBE\DatabaseLayering\Repositories\Providers\RepositoryProvider;
use ActivismeBE\DatabaseLayering\Tests\Resources\Models\User;
use ActivismeBE\DatabaseLayering\Tests\Resources\Repositories\UserRepository;
use Illuminate\Support\Collection;

class BaseTest extends \Orchestra\Testbench\TestCase
{
    protected $mock;
    protected $repository;
    
    public function setUp()
    {
        parent::setUp();
        $this->mock         = m::mock('Eloquent');
        $this->repository   = new UserRepository($this->app, new Collection());

        $this->setUpDatabase($this->app);
    }

    protected function getPackageProviders($app)
    {
        return [RepositoryProvider::class];
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

    protected function setUpDatabase($app)
    {
        $app['db']->connection()->getSchemaBuilder()->create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('email')->nullable();
            $table->string('password')->nullable();
            $table->timestamps();
        });

        $app[User::class]->insert([
            'first_name' => 'firstname',
            'last_name'  => 'lastname',
            'email'      => 'email@example.tld',
            'password'   => 'secret',
        ]);
    }
}
