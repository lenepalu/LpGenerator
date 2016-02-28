<?php

namespace LenePalu\LpGenerator\Commands;

use Illuminate\Console\GeneratorCommand;

class LpControllerCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:controller
                            {name : The name of the controller.}
                            {--model-name= : The name of the Model.}
                            {--view-path= : The name of the view path.}
                            {--required-fields= : Required fields for validations.}
                            {--route-group= : Prefix of the route group.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new resource controller.';

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
        return config('lp-generator.custom_template')
        ? config('lp-generator.path') . '/controller.stub'
        : __DIR__ . '/../stubs/controller.stub';
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string $rootNamespace
     *
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace . '\Http\Controllers';
    }

    /**
     * Build the model class with the given name.
     *
     * @param  string  $name
     *
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = $this->files->get($this->getStub());

        $viewPath = $this->option('view-path') ? $this->option('view-path') . '.' : '';

        list($pName,$sName) = LpCommand::ExtractPluralAndSingularFromName($name);
        $className  = studly_case($sName);
        $viewName = str_slug($sName,'-');
        $sVarName = snake_case($sName,'-');
        $pVarName = snake_case($pName,'-');
        $modelName = $this->option('model-name');
        $routeGroup = ($this->option('route-group')) ? $this->option('route-group') . '/' : '';

        $validationRules = '';
        if ($this->option('required-fields') != '') {
            $validationRules = "\$this->validate(\$request, " . $this->option('required-fields') . ");\n";
        }

        return $this->replaceNamespace($stub, $className)
            ->replaceViewPath($stub, $viewPath)
            ->replaceSVarName($stub, $sVarName)
            ->replacePVarName($stub, $pVarName)
            ->replaceViewName($stub, $viewName)
            ->replaceModelName($stub, $modelName)
            ->replaceRouteGroup($stub, $routeGroup)
            ->replaceValidationRules($stub, $validationRules)
            ->replaceClass($stub, $className);
    }

    /**
     * Replace the viewPath for the given stub.
     *
     * @param  string  $stub
     * @param  string  $viewPath
     *
     * @return $this
     */
    protected function replaceViewPath(&$stub, $viewPath)
    {
        $stub = str_replace(
            '{{viewPath}}', $viewPath, $stub
        );

        return $this;
    }

    /**
     * Replace the crudName for the given stub.
     *
     * @param  string $stub
     * @param $sVarName
     * @return $this
     * @internal param string $crudName
     *
     */
    protected function replaceSVarName(&$stub, $sVarName)
    {
        $stub = str_replace(
            '{{sVarName}}', $sVarName, $stub
        );

        return $this;
    }

    /**
     * Replace the crudName for the given stub.
     *
     * @param  string $stub
     * @param $pVarName
     * @return $this
     * @internal param string $crudName
     *
     */
    protected function replacePVarName(&$stub, $pVarName)
    {
        $stub = str_replace(
            '{{pVarName}}', $pVarName, $stub
        );

        return $this;
    }


    /**
     * Replace the crudNameSingular for the given stub.
     *
     * @param  string $stub
     * @param $viewName
     * @return $this
     * @internal param string $crudNameSingular
     *
     */
    protected function replaceViewName(&$stub, $viewName)
    {
        $stub = str_replace(
            '{{viewName}}', $viewName, $stub
        );

        return $this;
    }

    /**
     * Replace the modelName for the given stub.
     *
     * @param  string  $stub
     * @param  string  $modelName
     *
     * @return $this
     */
    protected function replaceModelName(&$stub, $modelName)
    {
        $stub = str_replace(
            '{{modelName}}', $modelName, $stub
        );

        return $this;
    }

    /**
     * Replace the routeGroup for the given stub.
     *
     * @param  string  $stub
     * @param  string  $routeGroup
     *
     * @return $this
     */
    protected function replaceRouteGroup(&$stub, $routeGroup)
    {
        $stub = str_replace(
            '{{routeGroup}}', $routeGroup, $stub
        );

        return $this;
    }

    /**
     * Replace the validationRules for the given stub.
     *
     * @param  string  $stub
     * @param  string  $validationRules
     *
     * @return $this
     */
    protected function replaceValidationRules(&$stub, $validationRules)
    {
        $stub = str_replace(
            '{{validationRules}}', $validationRules, $stub
        );

        return $this;
    }

}
