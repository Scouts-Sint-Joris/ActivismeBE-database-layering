<?php 

namespace ActivismeBE\DatabaseLayering\Repositories\Console\Commands;

use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators\RepositoryCreator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeRepositoryCommand
 *
 * @package ActivismeBE\DatabaseLayering\Repositories\Console\Commands
 */
class MakeRepositoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:repository';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new repository class';

    /**
     * @var RepositoryCreator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param RepositoryCreator $creator
     */
    public function __construct(RepositoryCreator $creator)
    {
        parent::__construct();

        $this->creator  = $creator;             // Set the creator
        $this->composer = app()['composer'];    // Set Composer
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();                  // Get the arguments.
        $options   = $this->option();                    // Get the options.
        
        $this->writeRepository($arguments, $options);    // Write repository.
        $this->composer->dumpAutoloads();                // Dump autoload.
    }

    /**
     * @param $arguments
     * @param $options
     */
    protected function writeRepository($arguments, $options)
    {
        $repository = $arguments['repository'];     // Set repository.
        $model      = $options['model'];            // Set model.

        if ($this->creator->create($repository, $model)) { // Create the repository.
            $this->info("Successfully created the repository class"); // Information message.
        }
    }

    /**
    * Get the console command arguments.
    *
    * @return array
    */
    protected function getArguments()
    {
        return [['repository', InputArgument::REQUIRED, 'The repository name.']];
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [['model', null, InputOption::VALUE_OPTIONAL, 'The model name.', null]];
    }
}
