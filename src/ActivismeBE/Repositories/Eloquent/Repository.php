<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Eloquent;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Container\Container as App;

use ActivismeBE\DatabaseLayering\Repositories\Contracts\RepositoryInterface;
use ActivismeBE\DatabaseLayering\Repositories\Contracts\CriteriaInterface;
use ActivismeBE\DatabaseLayering\Repositories\Exceptions\RepositoryException;
use ActivismeBE\DatabaseLayering\Repositories\Criteria\Criteria;

/**
 * Class Repository
 * 
 * @category 
 * @author 
 * @author
 * @license  MIT License <https://cpsb.github.io/ActivismeBE-database-layering/license>
 * @link     https://cpsb.github.io/ActivismeBE-database-layering/license
 * @package ActivismeBE\DatabaseLayering\Eloquent
 */
abstract class Repository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var Model
     */
    protected $model;

    /**
     * @var Collection
     */
    protected $newModel;

    /**
     * @var Collection
     */
    protected $criteria;

    /**
     * @var bool
     */
    protected $skipCriteria = false;

    /**
     * Prevents from overwriting same criteria in chain usage.
     *
     * @var bool
     */
    protected $preventCriteriaOverwriting = true;
     
    /**
     * @param App $app
     * @param Collection $collection
     * 
     * @throws \ActivismeBE\DatabaseLayering\Repositories\Exceptions\RepositoryException
     */
    public function __construct(App $app, Collection $collection)
    {
        $this->app      = $app;
        $this->criteria = $collection;

        $this->resetScope();
        $this->makeModel();
    }

    /**
     * Specify Model class name
     *
     * @return mixed
     */
    public abstract function model();

    /**
     * Get the base enttiy form the model.
     * 
     * @return \Illuminate\Database\Eloquent\Model 
     */
    public function entity() 
    {
        return $this->newModel;
    }
    
    /**
     * Get all the records form the database table.
     *
     * @param array $columns The database column names u want to use in your view.
     * 
     * @return mixed
     */
    public function all($columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->get($columns);
    
    }

    /**
     * Apply database relations on the query.
     *
     * @param array $relations The relations u want to apply on your query.
     * 
     * @return $this
     */
    public function with(array $relations)
    {
        $this->model = $this->model->with($relations);
        return $this;
    }

    /**
     * Lists all the values based on key and column.
     *
     * @param string $value The value for the lists function.
     * @param string $key   The key for the lists function. 
     * 
     * @deprecated deprecated in Laravel 5.3
     * 
     * @return array
     */
    public function lists($value, $key = null)
    {
        $this->applyCriteria();
        $lists = $this->model->lists($value, $key);

        if (is_array($lists)) {
            return $lists;
        }

        return $lists->all();
    }

    /**
     * Paginate the database results from the query.
     *
     * @param integer $perPage The data records u want to display per page. 
     * @param array   $columns Te database columns u want to use in the view.
     * 
     * @return mixed
     */
    public function paginate($perPage = 25, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * Create a new data record in the database.
     *
     * @param array $data The data fields u want to store in the database table.
     * 
     * @return mixed
     */
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    /**
     * Save a model without mass assignment
     *
     * @see  \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testSaveModel()
     *
     * @param array $data
     * 
     * @return bool
     */
    public function saveModel(array $data)
    {
        foreach ($data as $key => $value) {
            $this->model->$key = $value;
        }

        return $this->model->save();
    }

    /**
     * Update a record in the database table.
     *
     * @param array  $data
     * @param int    $id
     * @param string $attribute
     * 
     * @return mixed
     */
    public function update(array $data, $id, $attribute = "id")
    {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * Update database records through the eloquent fill method.
     *
     * @param  array     $data the data fields u want to 
     * @param  integer   $id   
     * 
     * @return mixed
     */
    public function updateRich(array $data, $id)
    {
        if (! ($model = $this->model->find($id))) {
            return false;
        }

        return $model->fill($data)->save();
    }

    /**
     * Delete a record in the database.
     *
     * @see \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testDeleteData()
     *
     * @param  int   $id        The resource id in the database.
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    /**
     * Find a record in the database based on the primary key.
     *
     * @see \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testFindAllColumns()
     * @see \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testFindSpecificColumns()
     *
     * @param  int   $id        The resource id in the database.
     * @param  array $columns   The database columns u want to use.
     
     * @return mixed
     */
    public function find($id, $columns = array('*'))
    {
        $this->applyCriteria();
        return $this->model->find($id, $columns);
    }

    /**
     * Try to find a record in the database table based on the primary key.
     *
     * @see \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testFindOrfailSuc()
     * @see \ActivismeBE\DatabaseLayering\Tests\Repositories\RepositoryTest::testFindOrFailErr()
     * 
     * @param integer $id The primary key in the database table.
     * 
     * @return mixed
     */
    public function findOrFail($id) 
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Find the first record in the database based on column and value.
     *
     * @param string $attribute The database column name.
     * @param string $value     The value that u want to find in the database table.
     * @param array  $columns   The database columns u want to use.
     * 
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    /**
     * Find all the records in the database bases on column and value.
     *
     * @param string $attribute The database column name.
     * @param string $value     The value where u want to search on.
     * @param array  $columns   The database columns want to use.
     * 
     * @return mixed
     */
    public function findAllBy($attribute, $value, $columns = ['*'])
    {
        $this->applyCriteria();
        return $this->model->where($attribute, '=', $value)->get($columns);
    }

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param array $where   The where criteria for the find query.
     * @param array $columns The database table columns u want to use in your view.
     * @param bool  $or      Enable of disable setter for OR WHERE queries. 
     *
     * @return \Illuminate\Database\Eloquent\Collection|null
     */
    public function findWhere($where, $columns = ['*'], $or = false)
    {
        $this->applyCriteria();
        $model = $this->model;
        foreach ($where as $field => $value) {
            if ($value instanceof \Closure) {
                $model = (!$or)
                    ? $model->where($value)
                    : $model->orWhere($value);
            } elseif (is_array($value)) {
                if (count($value) === 3) {
                    list($field, $operator, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, $operator, $search)
                        : $model->orWhere($field, $operator, $search);
                } elseif (count($value) === 2) {
                    list($field, $search) = $value;
                    $model = (!$or)
                        ? $model->where($field, '=', $search)
                        : $model->orWhere($field, '=', $search);
                }
            } else {
                $model = (!$or)
                    ? $model->where($field, '=', $value)
                    : $model->orWhere($field, '=', $value);
            }
        }
        
        return $model->get($columns);
    }

    /**
     * Make a model instance in the repository.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     * @throws RepositoryException
     */
    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    /**
     * Set Eloquent Model to instantiate
     *
     * @param $eloquentModel
     * 
     * @throws RepositoryException
     * 
     * @return Model
     */
    public function setModel($eloquentModel)
    {
        $this->newModel = $this->app->make($eloquentModel);
        
        if (! $this->newModel instanceof Model) {
            throw new RepositoryException(
                "Class {$this->newModel} must be an instance of Illuminate\\Database\\Eloquent\\Model"
            );
        }

        return $this->model = $this->newModel;
    }

    /**
     * Reset the query scope.
     *
     * @return $this
     */
    public function resetScope()
    {
        $this->skipCriteria(false);
        return $this;
    }

    /**
     * Skip the given criteria.
     *
     * @param  bool $status
     * 
     * @return $this
     */
    public function skipCriteria($status = true)
    {
        $this->skipCriteria = $status;
        return $this;
    }

    /**
     * Get the criteria for the database query.
     *
     * @return mixed
     */
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * Get records based on the repository call criteria.
     *
     * @param Criteria $criteria The criteria u want to take.
     * 
     * @return $this
     */
    public function getByCriteria(Criteria $criteria)
    {
        $this->model = $criteria->apply($this->model, $this);
        return $this;
    }

    /**
     * Push new query criteria in the interface call.
     *
     * @param Criteria $criteria The Criteria instance u want to apply.
     * 
     * @return $this
     */
    public function pushCriteria(Criteria $criteria)
    {
        if ($this->preventCriteriaOverwriting) { // Find existing criteria
            $key = $this->criteria->search(function ($item) use ($criteria) {
                return (is_object($item) && (get_class($item) == get_class($criteria)));
            });
           
            if (is_int($key)) { // Remove old criteria
                $this->criteria->offsetUnset($key);
            }
        }

        $this->criteria->push($criteria);
        return $this;
    }

    /**
     * Applies a new criteria in the repository call.
     *
     * @return $this
     */
    public function applyCriteria()
    {
        if ($this->skipCriteria === true)
            return $this;
        foreach ($this->getCriteria() as $criteria) {
            if ($criteria instanceof Criteria)
                $this->model = $criteria->apply($this->model, $this);
        }
        return $this;
    }
}