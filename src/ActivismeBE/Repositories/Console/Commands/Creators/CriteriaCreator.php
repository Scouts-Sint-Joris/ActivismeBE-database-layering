<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators;

use Doctrine\Common\Inflector\Inflector;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Config;

/**
 * Class CriteriaCreator
 *
 * @package ActivismeBE\DatabaseLayering\Console\Commands\Creators
 */
class CriteriaCreator
{
    protected $files;       /** @var Filesystem */
    protected $criteria;    /** @var Criteria   */
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
    public function getCriteria()
    {
        return $this->criteria;
    }

    /**
     * @param mixed $criteria
     */
    public function setCriteria($criteria)
    {
        $this->criteria = $criteria;
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
     * Create the criteria.
     *
     * @param $criteria
     * @param $model
     *
     * @return int
     */
    public function create($criteria, $model)
    {
        $this->setCriteria($criteria);  // Set the criteria.
        $this->setModel($model);        // Set the model.
        $this->createDirectory();       // Create the folder directory.
       
        return $this->createClass();  // Return result.
    }

    /**
     * Create the criteria directory.
     */
    public function createDirectory()
    {
        // Directory
        $directory = $this->getDirectory();
        
        if (! $this->files->isDirectory($directory)) { // Check if the directory exists.
            $this->files->makeDirectory($directory, 0755, true); // Create the directory if not.
        }
    }

    /**
     * Get the criteria directory.
     *
     * @return string
     */
    public function getDirectory()
    {
        $model = $this->getModel();                             // Model
        $directory = Config::get('repositories.criteria_path'); // Get the criteria path from the config file.

        if (isset($model) && !empty($model)) { // Check if the model is not null.
            $directory .= DIRECTORY_SEPARATOR . $this->pluralizeModel(); // // Update the directory with the model name.
        }
        
        return $directory; // Return the directory.
    }

    /**
     * Get the populate data.
     *
     * @return array
     */
    protected function getPopulateData()
    {
        $criteria =  $this->getCriteria();      // Criteria.
        $model    = $this->pluralizeModel();    // Model

        $criteria_namespace = Config::get('repositories.criteria_namespace');  // Criteria namespace.
        $criteria_class     = $criteria; // Criteria class.
        
        if (isset($model) && !empty($model)) {  // Check if the model isset and not empty.
            $criteria_namespace .= '\\' . $model; // Update the criteria namespace with the model folder.
        }

        $populate_data = [ // Populate data.
            'criteria_namespace' => $criteria_namespace,
            'criteria_class'     => $criteria_class
        ];
    
        return $populate_data; // Return the populate data.
    }

    /**
     * Get the path.
     *
     * @return string
     */
    protected function getPath()
    {
        return $this->getDirectory() . DIRECTORY_SEPARATOR . $this->getCriteria() . '.php'; // Return the path.
    }

    /**
     * Get the stub.
     *
     * @return string
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function getStub()
    {
        return $this->files->get($this->getStubPath() . "criteria.stub"); // Return the stub.
    }

    /**
     * Get the stub path.
     *
     * @return string
     */
    protected function getStubPath()
    {
        // TODO: Check if we can refactor this one.
    
        $path = __DIR__ . '/../../../../../../resources/stubs/'; // Path
        return $path; // Return the path.
    }

    /**
     * Populate the stub.
     *
     * @return mixed
     */
    protected function populateStub()
    {
        $populate_data = $this->getPopulateData();  // Populate data
        $stub = $this->getStub();                   // Stub
        
        foreach ($populate_data as $search => $replace) {   // Loop through the populate data.
            $stub = str_replace($search, $replace, $stub);  // Populate the stub.
        }
        
        return $stub; // Return the stub.
    }

    /**
     * Create the repository class.
     *
     * @return int
     */
    protected function createClass()
    {
        $result = $this->files->put($this->getPath(), $this->populateStub()); // Result.

        return $result; // Return the result.
    }

    /**
     * Pluralize the model.
     *
     * @return string
     */
    protected function pluralizeModel()
    {
        $pluralized = Inflector::pluralize($this->getModel());      // Pluralized
        $model_name = ucfirst($pluralized);                         // Uppercase first character the modelname
    
        return $model_name; // Return the pluralized model.
    }
}
