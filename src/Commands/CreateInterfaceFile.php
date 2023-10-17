<?php

namespace G4T\MockInterface\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateInterfaceFile extends Command
{
    protected $signature = 'interface:create {filename? : The name of the file (optional)}
                                {--all : Create All Schemas}
                                ';
    protected $description = 'Create a schema file in app/Mock/Schemas/';

    public function handle()
    {
        $configPath = config_path('interfaces.php');

        if (!File::exists($configPath)) {
            $this->error('The configuration file does not exist.');
            return;
        }

        $filename = $this->argument('filename');
        if (!$filename) {
            $filename = $this->ask('Please provide me Interface Name');
        }

        $model = null;
        $all = $this->option('all');
        if ($all) {
            $model = $this->ask('Please provide me Model Name');
        }

        if (!$filename) {
            $this->error('The interface name required.');
            return;
        }

        if ($all && !$model) {
            $this->error('The Model name required.');
            return;
        }

        if ($model) {
            $files = ["List", "Show", "Delete", "Create", "Update"];
            foreach ($files as $file) {
                $this->generateSchema($file, $model);
            }
        }



        $this->generateInterface($filename, $model);
    }


    public function getRoute($key, $model)
    {
        $path = Str::lower($model);
        $routes = [
            'List' => [
                'method' => 'get',
                'url' => "api/$path/list",
            ],
            'Show' => [
                'method' => 'get',
                'url' => "api/$path/show/{id}",
            ],
            'Delete' => [
                'method' => 'delete',
                'url' => "api/$path/delete/{id}",
            ],
            'Create' => [
                'method' => 'post',
                'url' => "api/$path/create",
            ],
            'Update' => [
                'method' => 'put',
                'url' => "api/$path/update/{id}",
            ],
        ];
        return $routes[$key];
    }


    public function generateInterface($name, $model)
    {
        $schemaPath = app_path("Mock/Interfaces/$name.php");
        if (is_null($model)) {
            $stubPath = __DIR__ . '/../stubs/interface_empty.stub';
        } else {
            $stubPath = __DIR__ . '/../stubs/interface_all.stub';
        }

        $directory = dirname($schemaPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($schemaPath)) {
            $this->error("Interface file '$name' already exists.");
        } else {
            $class_name = $this->getClassName($name);
            $stub = File::get($stubPath);
            if (!is_null($model)) {
                $url = Str::lower($model);
                $list_schema = $model . "\\" . $model . "List";
                $show_schema = "$model\Show{$model}";
                $delete_schema = "$model\Delete{$model}";
                $update_schema = "$model\Update{$model}";
                $create_schema = "$model\Create{$model}";
                $stub = str_replace('{{ListSchema}}', Str::studly($list_schema), $stub);
                $stub = str_replace('{{ShowSchema}}', Str::studly($show_schema), $stub);
                $stub = str_replace('{{DeleteSchema}}', Str::studly($delete_schema), $stub);
                $stub = str_replace('{{UpdateSchema}}', Str::studly($update_schema), $stub);
                $stub = str_replace('{{CreateSchema}}', Str::studly($create_schema), $stub);
                $stub = str_replace('{{CreateSchema}}', Str::studly($create_schema), $stub);
                $stub = str_replace('{{url}}', $url, $stub);
            }
            $stub = str_replace('{{class_name}}', Str::studly($class_name), $stub);
            File::put($schemaPath, $stub);
        }
    }

    public function generateSchema($key, $model)
    {
        if ($key == 'Create' || $key == 'Update' || $key == 'Delete') {
            $this->createMessageSchema($key, $model);
        } else {
            $this->createNormalSchema($key, $model);
        }
    }



    public function createMessageSchema($key, $model)
    {
        $filename = $key == 'List' ? $model . $key : $key . $model;
        $schemaPath = app_path("Mock/Schemas/$model/$filename.php");
        $directory = dirname($schemaPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($schemaPath)) {
            $this->error("Schema file '$filename' already exists.");
        } else {
            $class_name = $this->getClassName($filename);
            $message = $this->getMessage($key, $model);
            $stubPath = __DIR__ . '/../stubs/schema_message.stub';
            $stub = File::get($stubPath);
            $stub = str_replace('{{class_name}}', Str::studly($class_name), $stub);
            $stub = str_replace('{{model}}', Str::studly($model), $stub);
            $stub = str_replace('{{message}}', $message, $stub);
            File::put($schemaPath, $stub);
            $this->info("Schema file '$filename' created successfully.");
        }
    }


    public function getMessage($key, $model)
    {
        if ($key == 'Create') {
            $message = $model . " created";
        } else if ($key == 'Update') {
            $message = $model . " updated";
        } else if ($key == 'Delete') {
            $message = $model . " deleted";
        }
        return $message;
    }


    public function createNormalSchema($key, $model)
    {
        $filename = $key == 'List' ? $model . $key : $key . $model;
        $schemaPath = app_path("Mock/Schemas/$model/$filename.php");
        $directory = dirname($schemaPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($schemaPath)) {
            $this->error("Schema file '$filename' already exists.");
        } else {
            $class_name = $this->getClassName($filename);
            $stubPath = __DIR__ . '/../stubs/schema.stub';
            $stub = File::get($stubPath);
            $stub = str_replace('{{class_name}}', Str::studly($class_name), $stub);
            $stub = str_replace('{{model}}', Str::studly($model), $stub);
            File::put($schemaPath, $stub);
            $this->info("Schema file '$filename' created successfully.");
        }
    }


    public function getClassName($path)
    {
        $parts = explode('/', $path);
        return end($parts);
    }
}
