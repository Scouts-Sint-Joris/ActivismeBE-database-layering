<?php 

namespace ActivismeBE\DatabaseLayering\Contracts;

/**
 * Interface RepositoryInterface
 * 
 * @package ActivismeBE\DatabaseLayering\Contracts
 */
interface RepositoryInterface 
{
    /**
     * Get all the rows for the database table.
     *
     * @param  array $columns
     * @return mixed
     */
    public function all($columns = ['*']);

    /**
     * Paginate the database table results.
     *
     * @param  integer $perPage
     * @param  array   $columns
     * @return mixed
     */
    public function paginate($perPage = 1, $columns = ['*']);

    /**
     * Create a new row in the database table.
     *
     * @param array $data The data u want to store in the database.
     *
     * @return mixed
     */
    public function create(array $data);

    /**
     * Save a model without mass assignment
     *
     * @param array $data The data u want to store in the database.
     *
     * @return bool
     */
    public function saveModel(array $data);

    /**
     * Update a record in the database table.
     *
     * @param array   $data The input data fields u want to update in the db.
     * @param integer $id   The PK in the database.
     * 
     * @return mixed
     */
    public function update(array $data, $id);

    /**
     * Delete a record in the database.
     *
     * @param integer $id The resource id in the database. (PK)
     * 
     * @return mixed
     */
    public function delete($id);

    /**
     * Find a record in the database based on the primary key.
     *
     * @param integer $id      The resource id in the database. (PK)
     * @param array   $columns The db table columns that u want to use in your view.
     * 
     * @return mixed
     */
    public function find($id, $columns = ['*']);

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param string $field
     * @param string $value
     * @param array  $columns
     * 
     * @return mixed
     */
    public function findBy($field, $value, $columns = ['*']);

    /**
     * Find a database record by value and column. 
     * 
     * @param string $field   The database column name.
     * @param string $value   The value in the database column.
     * @param array  $columns The database table columns u want to use in your view.
     * 
     * @return mixed
     */
    public function findAllBy($field, $value, $columns = ['*']);

    /**
     * Find a collection of models by the given query conditions.
     *
     * @param  array $where
     * @param  array $columns
     * 
     * @return mixed
     */
    public function findWhere($where, $columns = ['*']);
}