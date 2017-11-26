<?php

namespace ActivismeBE\DatabaseLayering\Repositories\Console\Commands;

use ActivismeBE\DatabaseLayering\Repositories\Console\Commands\Creators\CriteriaCreator;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class MakeCriteriaCommand
 *
 * @package ActivismeBE\DatabaseLayering\Console\Commands
 */
class MakeCriteriaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $name = 'make:criteria';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new criteria class';

    /**
     * @var Creator
     */
    protected $creator;

    /**
     * @var Composer
     */
    protected $composer;

    /**
     * @param CriteriaCreator $creator
     */
    public function __construct(CriteriaCreator $creator)
    {
        parent::__construct();

        $this->creator  = $creator;             // Set the creator.
        $this->composer = app()['composer'];    // Set the composer.
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $arguments = $this->argument();                 // Get the arguments.
        $options   = $this->option();                   // Get the options.
      
        $this->writeCriteria($arguments, $options);     // Write criteria.
        $this->composer->dumpAutoloads();               // Dump autoload.
    }

    /**
     * Write the criteria.
     *
     * @param $arguments
     * @param $options
     */
    public function writeCriteria($arguments, $options)
    {
        $criteria = $arguments['criteria'];     // Set criteria
        $model    = $options['model'];          // Set model.

        if ($this->creator->create($criteria, $model)) { // Create the criteria.
            $this->info("Succesfully created the criteria class."); // Information message.
        }
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [['criteria', InputArgument::REQUIRED, 'The criteria name.']];
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
