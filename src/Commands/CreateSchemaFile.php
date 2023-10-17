<?php

namespace G4T\MockInterface\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class CreateSchemaFile extends Command
{
    protected $signature = 'schema:create {filename? : The name of the file (optional)}
                                {--model= : Create model}
                                {--interface : Create Interface}
                                ';
    protected $description = 'Create a schema file in app/Mock/Schemas/';

    public function handle()
    {
        $filename = $this->argument('filename');
        if(!$filename) {
            $filename = $this->ask('Please provide me Schema Name');
        }

        $model = $this->option('model');
        if(!$model) {
            $model = $this->ask('Please provide me Model Name');
        }

        if (!$filename) {
            $this->error('The file name required');
            return;
        }

        if (!$model) {
            $this->error('The --model option is required. Use --model=ModelName.');
            return;
        }


        $interface = $this->option('interface');
        if($interface) {
            $this->generateInterface($model);
        }
        
        $this->generateSchema($filename, $model);

    }

    public function generateInterface($model)
    {
        $interface = $model.'Interface';
        $schemaPath = app_path("Mock/Interfaces/$model/$interface.php");
        $stubPath = __DIR__.'/../stubs/interface_empty.stub';

        $directory = dirname($schemaPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($schemaPath)) {
            $this->error("Interface file '$interface' already exists.");
        } else {
            $class_name = $this->getClassName($interface);
            $stub = File::get($stubPath);
            $stub = str_replace('{{class_name}}', Str::studly($class_name), $stub);
            File::put($schemaPath, $stub);
        }
    }

    public function generateSchema($filename, $model)
    {
        $schemaPath = app_path("Mock/Schemas/$model/$filename.php");
        $stubPath = __DIR__.'/../stubs/schema.stub';

        $directory = dirname($schemaPath);

        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0755, true);
        }

        if (File::exists($schemaPath)) {
            $this->error("Schema file '$filename' already exists.");
        } else {
            $class_name = $this->getClassName($filename);
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
