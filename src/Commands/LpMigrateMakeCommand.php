<?php
/**
 * Created by PhpStorm.
 * User: LenePalu
 * Date: 2/29/16
 * Time: 20:45
 */
namespace LenePalu\LpGenerator\Commands;

use Illuminate\Database\Console\Migrations\BaseCommand;
use Illuminate\Support\Composer;
use Illuminate\Database\Migrations\MigrationCreator;

class LpMigrateMakeCommand extends BaseCommand
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'lp:migration {name : The name of the migration.}
        {--create= : The table to be created.}
        {--schema= : The table schema.}
        {--pk=id : The name of the primary key.}
        {--table= : The table to migrate.}
        {--path= : The location where the migration file should be created.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new migration file';

    /**
     * The migration creator instance.
     *
     * @var \Illuminate\Database\Migrations\MigrationCreator
     */
    protected $creator;

    /**
     * The Composer instance.
     *
     * @var \Illuminate\Support\Composer
     */
    protected $composer;

    /**
     * Create a new migration install command instance.
     *
     * @param MigrationCreator|LpMigrationCreator $creator
     * @param  \Illuminate\Support\Composer $composer
     */
    public function __construct(LpMigrationCreator $creator, Composer $composer)
    {
        parent::__construct();

        $this->creator = $creator;
        $this->composer = $composer;
    }

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        // It's possible for the developer to specify the tables to modify in this
        // schema operation. The developer may also specify if this table needs
        // to be freshly created so we can create the appropriate migrations.
        $name = $this->input->getArgument('name');

        $table = $this->input->getOption('table');

        $create = $this->input->getOption('create');

        $schema = $this->input->getOption('schema');

        $primaryKey = $this->input->getOption('pk');

        if (!$table && is_string($create)) {
            $table = $create;
        }

        // Now we are ready to write the migration out to disk. Once we've written
        // the migration out, we will dump-autoload for the entire framework to
        // make sure that the migrations are registered by the class loaders.
        $this->writeMigration($name, $table, $create, $schema, $primaryKey);

        $this->composer->dumpAutoloads();
    }

    /**
     * Write the migration file to disk.
     *
     * @param  string $name
     * @param  string $table
     * @param  bool $create
     * @param $schema
     * @param $primaryKey
     * @return string
     */
    protected function writeMigration($name, $table, $create, $schema, $primaryKey)
    {
        $path = $this->getMigrationPath();

        $file = pathinfo($this->creator->lpCreate($name, $path, $table, $create, $this->parseSchema($schema), $primaryKey), PATHINFO_FILENAME);

        $this->line("<info>Created Migration:</info> $file");
    }

    /**
     * Get migration path (either specified by '--path' option or default location).
     *
     * @return string
     */
    protected function getMigrationPath()
    {
        if (!null === $targetPath = $this->input->getOption('path')) {
            return $this->laravel->basePath() . '/' . $targetPath;
        }

        return parent::getMigrationPath();
    }

    protected function parseSchema($schema)
    {

        $fields = explode(',', $schema);
        $data = array();
        if ($schema) {
            $x = 0;
            foreach ($fields as $field) {
                $fieldArray = explode(':', $field);
                $data[$x]['name'] = camel_case(trim($fieldArray[0]));
                $data[$x]['type'] = trim($fieldArray[1]);
                $x++;
            }
        }

        $schemaFields = '';
        foreach ($data as $item) {
            switch ($item['type']) {
                case 'char':
                    $schemaFields .= "\$table->char('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'date':
                    $schemaFields .= "\$table->date('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'datetime':
                    $schemaFields .= "\$table->dateTime('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'time':
                    $schemaFields .= "\$table->time('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'timestamp':
                    $schemaFields .= "\$table->timestamp('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'text':
                    $schemaFields .= "\$table->text('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'mediumtext':
                    $schemaFields .= "\$table->mediumText('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'longtext':
                    $schemaFields .= "\$table->longText('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'json':
                    $schemaFields .= "\$table->json('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'jsonb':
                    $schemaFields .= "\$table->jsonb('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'binary':
                    $schemaFields .= "\$table->binary('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'number':
                case 'integer':
                    $schemaFields .= "\$table->integer('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'bigint':
                    $schemaFields .= "\$table->bigInteger('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'mediumint':
                    $schemaFields .= "\$table->mediumInteger('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'tinyint':
                    $schemaFields .= "\$table->tinyInteger('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'smallint':
                    $schemaFields .= "\$table->smallInteger('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'boolean':
                    $schemaFields .= "\$table->boolean('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'decimal':
                    $schemaFields .= "\$table->decimal('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'double':
                    $schemaFields .= "\$table->double('" . $item['name'] . "');\n\t\t\t";
                    break;

                case 'float':
                    $schemaFields .= "\$table->float('" . $item['name'] . "');\n\t\t\t";
                    break;

                default:
                    $schemaFields .= "\$table->string('" . $item['name'] . "');\n\t\t\t";
                    break;
            }
        }

        return trim($schemaFields, "\n\t\t\t");
    }
}
