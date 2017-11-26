<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class RepositoryCreator
 *
 * @package ActivismeBE\DatabaseLayering\Console\Commands\Creators
 */
class RepositoryCreator
{
    protected $files;       /** @var FileSystem */
    protected $repository;  /** @var Repository */
    protected $model;       /** @var Model      */
 
    /**
     * @param Filesystem $files
     */
    public function __construct(Filesystem $files)
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getRepository()
    {
        return $this->repository;
    }

    /**
     * @param mixed $repository
     */
    public function setRepository($repository)
    {
        $this->repository = $repository;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    /**
     * @param mixed $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * Create the repository.
     *
     * @param $repository
     * @param $model
     * @return int
     */
    public function create($repository, $model)
    {
        $this->setRepository($repository);  // Set the repository.
        $this->setModel($model);            // Set the model.
        $this->createDirectory();           // Create the directory.
        
        return $this->createClass(); // Return result.
    }

    /**
     * Create a new directory if the directory doesn't exists.
     *
     * @return void
     */
    protected function createDirectory()
    {
        $directory = $this->getDirectory();  // Directory.
       
        if (! $this->files->isDirectory($directory)) { // Check if the directory exists.
            $this->files->makeDirectory($directory, 0755, true); // Create the directory if not.
        }
    }

    /**
     * Get the repository directory.
     *
     * @return mixed
     */
    protected function getDirectory()
    {
        return Config::get('repositories.repository_path'); // Return the directory.
    }

    /**
     * Get the repository name.
     *
     * @return mixed|string
     */
    protected function getRepositoryName()
    {
        $repository_name = $this->getRepository(); // Get the repository.
        
        if (! strpos($repository_name, 'Repository') !== false) { // Check if the repository ends with 'Repository'.
            $repository_name .= 'Repository'; // Append 'Repository' if not.
        }

        return $repository_name; // Return repository name.
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    protected function getModelName()
    {
        $model  = $this->getModel(); // Set model.
    
        if (isset($model) && !empty($model)) { // Check if the model isset.
            $model_name = $model; // Set the model name from the model option.
        } else { // Set the model name by the stripped repository name.
            $model_name = Inflector::singularize($this->stripRepositoryName());
        }
        
        return $model_name; // Return the model name.
    }

    /**
     * Get the stripped repository name.
     *
     * @return string
     */
    protected function stripRepositoryName()
    {
        $repository = strtolower($this->getRepository());           // Lowercase the repository.
        $stripped   = str_replace("repository", "", $repository);   // Remove repository from the string.
        $result     = ucfirst($stripped);                           // Uppercase repository name.
        
        return $result; // Return the result.
    }

    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $repository_namespace = Config::get('repositories.repository_namespace');   // Repository namespace.
        $model_path           = Config::get('repositories.model_namespace');        // Model path.

        $repository_class     = $this->getRepositoryName(); // Repository class.
        $model_name           = $this->getModelName();      // Model name.

        // Populate data.
        $populate_data = ['repository_namespace' => $repository_namespace, 'repository_class' => $repository_class, 'model_path' => $model_path, 'model_name' => $model_name];
        // Return populate data.
        return $populate_data;
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getRepositoryName() . '.php'; // return path.
    }

    /**
     * Get the stub.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath() . "repository.stub"); // Return stub.
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStubPath()
    {
        // TODO: Check if we can refactor this method.
        // Stub path.
        $stub_path = __DIR__ . '/../../../../../../resources/stubs/';
        // Return the stub path.
        return $stub_path;
    }

    /**
     * Populate the stub.
     *
     * @return mixed
     */
    protected function populateStub()
    {
        $populate_data  = $this->getPopulateData();     // Populate data
        $stub           = $this->getStub();             // Stub

        foreach ($populate_data as $key => $value) {  // Loop through the populate data.
            $stub = str_replace($key, $value, $stub); // Populate the stub.
        }
    
        return $stub; // Return the stub.
    }

    /**
     * Set the class to the file.
     *
     * @return void
     */
    protected function createClass()
    {
        return $this->files->put($this->getPath(), $this->populateStub()); // Return the result.
    }
}
