<?php

namespace LenePalu\LpGenerator\Commands;

use File;
use Illuminate\Console\Command;
use Illuminate\Support\Str;


class LpCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:generate
                            {name : The name of the Crud.}
                            {--fields= : Fields name for the form & model.}
                            {--route=yes : Include Crud route to routes.php? yes|no.}
                            {--soft-delete=yes : use softDelete? yes|no.}
                            {--pk=id : The name of the primary key.}
                            {--view-path= : The name of the view path.}
                            {--namespace= : Namespace of the controller.}
                            {--route-group= : Prefix of the route group.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Crud including controller, model, views & migrations.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        list($pName,$sName) = $this->ExtractPluralAndSingularFromName($this->argument('name'));
        $modelName = studly_case($sName);
        $name = $modelName;
        $migrationName = str_slug($pName,'_');
        $tableName = $migrationName;
        $viewName = str_slug($pName,'-');

        $routeGroup = $this->option('route-group');
        $routeName = ($routeGroup) ? $routeGroup . '/' . $viewName : $viewName;

        $controllerNamespace = ($this->option('namespace')) ? $this->option('namespace') . '\\' : '';

        $fields = $this->option('fields');
        $primaryKey = $this->option('pk');
        $viewPath = $this->option('view-path');

        $fieldsArray = explode(',', $fields);

        $requiredFieldsStr = '';

        foreach ($fieldsArray as $item) {
            $fillableArray[] = preg_replace("/(.*?):(.*)/", "$1", trim($item));

            $itemArray = explode(':', $item);
            $currentField = trim($itemArray[0]);
            $requiredFieldsStr .= (isset($itemArray[2])
                && (trim($itemArray[2]) == 'req'
                    || trim($itemArray[2]) == 'required'))
                ? "'$currentField' => 'required', " : '';
        }

        if (!empty($fillableArray)) {
            $commaSeparatedString = implode("', '", $fillableArray);
        }
        $fillable = empty($commaSeparatedString) ? [] : "['" . $commaSeparatedString . "']";

        $requiredFields = ($requiredFieldsStr != '') ? "[" . $requiredFieldsStr . "]" : '';

        $this->call('lp:controller', ['name' => $controllerNamespace . $name . 'Controller', '--crud-name' => $name, '--model-name' => $modelName, '--view-path' => $viewPath, '--required-fields' => $requiredFields, '--route-group' => $routeGroup]);
        $this->call('lp:model', ['name' => $modelName, '--fillable' => $fillable, '--table' => $tableName]);
        $this->call('lp:migration', ['name' => $migrationName, '--schema' => $fields, '--pk' => $primaryKey]);
        $this->call('lp:view', ['name' => $viewName, '--fields' => $fields, '--view-path' => $viewPath, '--route-group' => $routeGroup]);

        // Updating the Http/routes.php file
        $routeFile = app_path('Http/routes.php');
        if (file_exists($routeFile) && (strtolower($this->option('route')) === 'yes')) {
            $controller = ($controllerNamespace != '') ? $controllerNamespace . '\\' . $name . 'Controller' : $name . 'Controller';

            if (\App::VERSION() >= '5.2') {
                $isAdded = File::append($routeFile,
                    "\nRoute::group(['middleware' => ['web']], function () {"
                    . "\n\tRoute::resource('" . $routeName . "', '" . $controller . "');"
                    . "\n});"
                );
            } else {
                $isAdded = File::append($routeFile, "\nRoute::resource('" . $routeName . "', '" . $controller . "');");
            }

            if ($isAdded) {
                $this->info('Crud/Resource route added to ' . $routeFile);
            } else {
                $this->info('Unable to add the route to ' . $routeFile);
            }
        }
    }

    public static function ExtractPluralAndSingularFromName($name){
        $pName = '';
        $sName = '';
        $wTab = explode('_' , str_slug(class_basename($name),'_'));
        foreach($wTab as $w){
            $pName .= str_plural($w)." ";
            $sName .= str_singular($w)." ";
        }
        $pName = trim($pName,' ');
        $sName = trim($sName,' ');
        return [$pName,$sName];
    }


}
