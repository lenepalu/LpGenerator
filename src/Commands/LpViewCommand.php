<?php

namespace LenePalu\LpGenerator\Commands;

use File;
use Illuminate\Console\Command;

class LpViewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lp:view
                            {name : The name of the Crud.}
                            {--fields= : The fields name for the form.}
                            {--view-path= : The name of the view path.}
                            {--route-group= : Prefix of the route group.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create views for the Crud.';

    /**
     * View Directory Path.
     *
     * @var string
     */
    protected $viewDirectoryPath;

    /**
     *  Form field types collection.
     *
     * @var array
     */
    protected $typeLookup = [
        'string' => 'text',
        'char' => 'text',
        'varchar' => 'text',
        'text' => 'textarea',
        'mediumtext' => 'textarea',
        'longtext' => 'textarea',
        'json' => 'textarea',
        'jsonb' => 'textarea',
        'binary' => 'textarea',
        'password' => 'password',
        'email' => 'email',
        'number' => 'number',
        'integer' => 'number',
        'bigint' => 'number',
        'mediumint' => 'number',
        'tinyint' => 'number',
        'smallint' => 'number',
        'decimal' => 'number',
        'double' => 'number',
        'float' => 'number',
        'date' => 'date',
        'datetime' => 'datetime-local',
        'time' => 'time',
        'boolean' => 'radio',
    ];

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();

        $this->viewDirectoryPath = config('lp-generator.custom_template')
        ? config('lp-generator.path')
        : __DIR__ . '/../stubs/';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        list($pName,$sName) = LpCommand::ExtractPluralAndSingularFromName($this->argument('name'));

        $viewName = str_slug($pName,'-');
        $pVarName = camel_case($pName);
        $sVarName = camel_case($sName);
        $title = ucwords($pName);

        $modelName = studly_case($sName);
        $routeGroup = ($this->option('route-group')) ? $this->option('route-group') . '/' : $this->option('route-group');

        $viewDirectory = config('view.paths')[0] . '/';
        if ($this->option('view-path')) {
            $userPath = $this->option('view-path');
            $path = $viewDirectory . $userPath . '/' . $viewName . '/';
        } else {
            $path = $viewDirectory . $viewName . '/';
        }

        if (!File::isDirectory($path)) {
            File::makeDirectory($path, 0755, true);
        }

        $fields = $this->option('fields');
        $fieldsArray = explode(',', $fields);

        $formFields = array();

        if ($fields) {
            $x = 0;
            foreach ($fieldsArray as $item) {
                $itemArray = explode(':', $item);
                $formFields[$x]['name'] = trim($itemArray[0]);
                $formFields[$x]['type'] = trim($itemArray[1]);
                $formFields[$x]['required'] = (isset($itemArray[2]) && (trim($itemArray[2]) == 'req' || trim($itemArray[2]) == 'required')) ? true : false;

                $x++;
            }
        }

        $formFieldsHtml = '';
        foreach ($formFields as $item) {
            $formFieldsHtml .= $this->createField($item);
        }

        // Form fields and label
        $formHeadingHtml = '';
        $formBodyHtml = '';
        $formBodyHtmlForShowView = '';

        $i = 0;
        foreach ($formFields as $key => $value) {

            $field = $value['name'];
            $label = ucwords(str_replace('_', ' ', $field));
            $formHeadingHtml .= '<th>' . $label . '</th>';
            $formBodyHtml .= '<td>{{ $item->' . $field . ' }}</td>';
            $formBodyHtmlForShowView .= '<td> {{ $%%crudNameSingular%%->' . $field . ' }} </td>';

            $i++;
        }

        // For index.blade.php file
        $indexFile = $this->viewDirectoryPath . 'index.blade.stub';
        $newIndexFile = $path . 'index.blade.php';
        $this->BuildViewFile($indexFile,$newIndexFile,$viewName,$sVarName,$pVarName,$title,$modelName,$routeGroup,$formFieldsHtml);


        // For create.blade.php file
        $createFile = $this->viewDirectoryPath . 'create.blade.stub';
        $newCreateFile = $path . 'create.blade.php';
        $this->BuildViewFile($createFile,$newCreateFile,$viewName,$sVarName,$pVarName,$title,$modelName,$routeGroup,$formFieldsHtml);


        // For edit.blade.php file
        $editFile = $this->viewDirectoryPath . 'edit.blade.stub';
        $newEditFile = $path . 'edit.blade.php';
        $this->BuildViewFile($editFile,$newEditFile,$viewName,$sVarName,$pVarName,$title,$modelName,$routeGroup,$formFieldsHtml);


        // For show.blade.php file
        $showFile = $this->viewDirectoryPath . 'show.blade.stub';
        $newShowFile = $path . 'show.blade.p$hp';
        $this->BuildViewFile($showFile,$newShowFile,$viewName,$sVarName,$pVarName,$title,$modelName,$routeGroup,$formFieldsHtml);

        // For layouts/master.blade.php file
        $layoutsDirPath = base_path('resources/views/layouts/');
        if (!File::isDirectory($layoutsDirPath)) {
            File::makeDirectory($layoutsDirPath);
        }

        $layoutsFile = $this->viewDirectoryPath . 'master.blade.stub';
        $newLayoutsFile = $layoutsDirPath . 'master.blade.php';

        if (!File::exists($newLayoutsFile)) {
            if (!File::copy($layoutsFile, $newLayoutsFile)) {
                echo "failed to copy $layoutsFile...\n";
            }
        }

        $this->info('View created successfully.');
    }

    /**
     * Form field wrapper.
     *
     * @param  string $item
     * @param  string $field
     * @return string
     */
    protected function wrapField($item, $field)
    {
        $formGroup =
            <<<EOD
            <div class="form-group {{ \$errors->has('%1\$s') ? 'has-error' : ''}}">
                {!! Form::label('%1\$s', '%2\$s: ', ['class' => 'col-sm-3 control-label']) !!}
                <div class="col-sm-6">
                    %3\$s
                    {!! \$errors->first('%1\$s', '<p class="help-block">:message</p>') !!}
                </div>
            </div>\n
EOD;

        return sprintf($formGroup, $item['name'], ucwords(strtolower(str_replace('_', ' ', $item['name']))), $field);
    }

    /**
     * Form field generator.
     *
     * @param  string  $item
     *
     * @return string
     */
    protected function createField($item)
    {
        switch ($this->typeLookup[$item['type']]) {
            case 'password':
                return $this->createPasswordField($item);
                break;
            case 'datetime-local':
            case 'time':
                return $this->createInputField($item);
                break;
            case 'radio':
                return $this->createRadioField($item);
                break;
            default: // text
                return $this->createFormField($item);
        }
    }

    /**
     * Create a specific field using the form helper.
     *
     * @param  string  $item
     *
     * @return string
     */
    protected function createFormField($item)
    {
        $required = ($item['required'] === true) ? ", 'required' => 'required'" : "";

        return $this->wrapField(
            $item,
            "{!! Form::" . $this->typeLookup[$item['type']] . "('" . $item['name'] . "', null, ['class' => 'form-control'$required]) !!}"
        );
    }

    /**
     * Create a password field using the form helper.
     *
     * @param  string  $item
     *
     * @return string
     */
    protected function createPasswordField($item)
    {
        $required = ($item['required'] === true) ? ", 'required' => 'required'" : "";

        return $this->wrapField(
            $item,
            "{!! Form::password('" . $item['name'] . "', ['class' => 'form-control'$required]) !!}"
        );
    }

    /**
     * Create a generic input field using the form helper.
     *
     * @param  string  $item
     *
     * @return string
     */
    protected function createInputField($item)
    {
        $required = ($item['required'] === true) ? ", 'required' => 'required'" : "";

        return $this->wrapField(
            $item,
            "{!! Form::input('" . $this->typeLookup[$item['type']] . "', '" . $item['name'] . "', null, ['class' => 'form-control'$required]) !!}"
        );
    }

    /**
     * Create a yes/no radio button group using the form helper.
     *
     * @param  string  $item
     *
     * @return string
     */
    protected function createRadioField($item)
    {
        $field =
            <<<EOD
            <div class="checkbox">
                <label>{!! Form::radio('%1\$s', '1') !!} Yes</label>
            </div>
            <div class="checkbox">
                <label>{!! Form::radio('%1\$s', '0', true) !!} No</label>
            </div>
EOD;

        return $this->wrapField($item, sprintf($field, $item['name']));
    }

    protected  function BuildViewFile($stub,$newView,$viewName,$sVarName,$pVarName,$title,$modelName,$routeGroup,$formFieldsHtml){
        if (!File::copy($stub, $newView)) {
            echo "failed to copy $stub...\n";
        } else {
            File::put($newView, str_replace('%%viewName%%', $viewName, File::get($newView)));
            File::put($newView, str_replace('%%sVarName%%', $sVarName, File::get($newView)));
            File::put($newView, str_replace('%%pVarName%%', $pVarName, File::get($newView)));
            File::put($newView, str_replace('%%title%%', $title, File::get($newView)));
            File::put($newView, str_replace('%%modelName%%', $modelName, File::get($newView)));
            File::put($newView, str_replace('%%routeGroup%%', $routeGroup, File::get($newView)));
            File::put($newView, str_replace('%%formFieldsHtml%%', $formFieldsHtml, File::get($newView)));
        }

    }

}
