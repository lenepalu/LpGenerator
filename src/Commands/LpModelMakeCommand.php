<?php

/**
 * Created by PhpStorm.
 * User: LenePalu
 * Date: 2/29/16
 * Time: 20:45
 */
namespace LenePalu\LpGenerator\Commands;

use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class LpModelMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lp:model2';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Eloquent model class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Model';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        if (parent::fire() !== false) {
            if ($this->option('migration')) {
                $table = Str::plural(Str::snake(class_basename($this->argument('name'))));

                $this->call('make:migration', ['name' => "create_{$table}_table", '--create' => $table]);
            }
        }
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/model.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace;
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['migration', 'm', InputOption::VALUE_OPTIONAL, 'Create a new migration file for the model.'],
            ['table', 't', InputOption::VALUE_OPTIONAL, 'Name of database table(Default:plural of model name)'],
            ['fillable', 'f', InputOption::VALUE_OPTIONAL, 'The fillable attributes'],
        ];
    }

    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $fillable = $this->option('fillable') ?: '';
        $table = $this->option('table') ?: str_plural(class_basename($name));
        $fillable = str_replace(['[', ']'], '', $fillable);
        $stub = str_replace(['{{fillable}}', '{{table}}'], [$fillable, $table], $stub);
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

    }


}
