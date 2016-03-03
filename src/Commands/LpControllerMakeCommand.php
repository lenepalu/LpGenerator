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

class LpControllerMakeCommand extends GeneratorCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'lp:controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new controller class';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Controller';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {

        return __DIR__ . '/../stubs/controller.stub';

    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'Specify model name.'],
            ['request', 'r', InputOption::VALUE_OPTIONAL, 'Specify request name.'],
            ['view-path', '', InputOption::VALUE_OPTIONAL, 'Specify path of views.(Default : resources/views)'],
            ['view-name', '', InputOption::VALUE_OPTIONAL, 'Specify crud views folder name.(Default: model name)'],
            ['route-group', '', InputOption::VALUE_OPTIONAL, 'Specify crud route group name.'],
        ];
    }

    /**
     * Build the class with the given name.
     *
     * Remove the base controller import if we are already in base namespace.
     *
     * @param  string $name
     * @return string
     */
    protected function buildClass($name)
    {
        $namespace = $this->getNamespace($name);
        $modelName = $this->option('model') ?: ucfirst(str_replace('Controller', '', class_basename($name)));
        $requestName = $this->option('request') ?: 'Request';
        $viewPath = $this->option('view-path') ?: '';
        $viewName = $this->option('view-name') ?: strtolower($modelName);
        $routeGroup = $this->option('route-group') ? $this->option('route-group') . '/' : '';
        $sVarName = str_singular(camel_case($modelName));
        $pVarName = str_plural($sVarName);
        $stub = $this->files->get($this->getStub());

        $stub = str_replace(
            ['use ' . $namespace . "\\Controller;\n", '{{modelName}}', '{{RequestName}}', '{{viewName}}', '{{viewPath}}', '{{routeGroup}}', '{{pVarName}}', '{{sVarName}}'],
            ['', $modelName, $requestName, $viewName, $viewPath, $routeGroup, $pVarName, $sVarName],
            $stub
        );

        return $this->replaceNamespace($stub, $name)
            ->replaceClass($stub, $name);
    }


}
