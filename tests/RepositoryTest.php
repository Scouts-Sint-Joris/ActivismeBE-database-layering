<?php

namespace ActivismeBE\DatabaseLayering\Tests\Repositories;

//error_reporting(E_ALL);
//ini_set("display_errors", 1);

use ActivismeBE\DatabaseLayering\Repositories\Providers\RepositoryProvider;
use ActivismeBE\DatabaseLayering\Tests\Resources\Models\User;
use ActivismeBE\DatabaseLayering\Tests\Resources\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Model;
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
        $this->mock = m::mock(Model::class);
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

        $app[User::class]->create(['first_name' => 'firstname']);
    }

    public function testFindAllColumns()
    {
        $mock = m::mock('Eloquent');
        $mock->shouldReceive('find')->with($userId = 1)->andReturn('first_name');

        $repo = new UserRepository($this->app, new Collection());
        $this->assertEquals('firstname', $repo->find($userId)->first_name);
    }
}