<?php
/**
 * Created by PhpStorm.
 * User: LenePalu
 * Date: 2/29/16
 * Time: 20:45
 */

namespace LenePalu\LpGenerator\Commands;


use Illuminate\Database\Migrations\MigrationCreator;

class LpMigrationCreator extends MigrationCreator
{

    /**
     * Get the path to the stubs.
     *
     * @return string
     */
    public function getStubPath()
    {
        return __DIR__ . '/../stubs/migration';
    }

    public function lpCreate($name, $path, $table = null, $create = false, $schema = '', $primaryKey = 'id')
    {
        $path = $this->getPath($name, $path);

        // First we will get the stub file for the migration, which serves as a type
        // of template for the migration. Once we have those we will populate the
        // various place-holders, save the file, and run the posts create event.
        $stub = $this->getStub($table, $create);

        $stub = str_replace(['{{SchemaPK}}', '{{SchemaUp}}'], [$primaryKey, $schema], $stub);
        //$stub = str_replace('{{SchemaPK}}',$primaryKey,$stub);
        //$stub = str_replace('{{SchemaUp}}',$schema,$stub);

        $this->files->put($path, $this->populateStub($name, $stub, $table));

        $this->firePostCreateHooks();

        return $path;

    }

}