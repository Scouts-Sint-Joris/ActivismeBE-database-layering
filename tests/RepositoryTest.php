<?php

namespace ActivismeBE\DatabaseLayering\Tests\Repositories;

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

use ActivismeBE\DatabaseLayering\Repositories\Providers\RepositoryProvider;
use ActivismeBE\DatabaseLayering\Tests\Resources\Models\User;
use ActivismeBE\DatabaseLayering\Tests\Resources\Repositories\UserRepository;
use \Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Collection;
use \Mockery as m;

class RepositoryTest extends \Orchestra\Testbench\TestCase
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
       //
    }

    public function testFindAllColumns()
    {
        $call = $this->repository->find(1);

        $this->assertEquals('firstname', $call->first_name);
        $this->assertEquals('lastname', $call->last_name);
        $this->assertEquals('email@example.tld', $call->email);
        $this->assertEquals('secret', $call->password);
    }

    public function testFindSpecificColumns()
    {
        $call = $this->repository->find(1, ['first_name', 'last_name']);

        $this->assertEquals('firstname', $call->first_name);
        $this->assertEquals('lastname', $call->last_name);
        $this->assertNull($call->password);
        $this->assertNull($call->email);
    }

    public function testDeleteData()
    {
        $this->repository->delete(1);
        $check = $this->repository->find(1);

        $this->assertNull($check);
    }

    public function testUpdateRich()
    {
        // TODO: Write test
    }

    public function testSaveModel()
    {
        // TODO: Write test
    }
}