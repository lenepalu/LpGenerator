<?php

/**
 * Created by PhpStorm.
 * User: LenePalu
 * Date: 2/29/16
 * Time: 20:45
 */
namespace LenePalu\LpGenerator\Commands;

use Illuminate\Console\GeneratorCommand;
use Symfony\Component\Console\Input\InputOption;

class LpRequestMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lp:request';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new form request class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Request';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../stubs/request.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Requests';
    }

    protected function getOptions()
    {
        return [
            ['roles', 'r', InputOption::VALUE_OPTIONAL, 'Specify request roles.'],
        ];
    }


    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());
        $roles = $this->option('roles') ?: '';
        $roles = str_replace(['[', ']', ','], ['', '', ',\n\t\t\t'], $roles);
        $stub = str_replace('{{Roles}}', $roles, $stub);
        return $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);
    }


}
