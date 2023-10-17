<?php

namespace G4T\MockInterface\Controllers;

use App\Http\Controllers\Controller;
use G4T\MockInterface\Helpers\Helper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class MockController extends Controller
{
    use Helper;

    public function index(Request $request)
    {
        $directory = public_path('/../app/Mock/Interfaces');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $interfaces = $this->createNamespace($file, $directory);
                $data = $this->createDataUsingInterface($request, $interfaces);
            }
        }
        return $data;
    }


    public function getRoutes()
    {
        $directory = public_path('/../app/Mock/Interfaces');
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));
        $routes = [];
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'php') {
                $interface = $this->createNamespace($file, $directory);
                $interfaceRoutes = $this->getFunctionsFromInterface($interface);
                if($interfaceRoutes) {
                    $routes = array_merge($routes, $interfaceRoutes);
                }
            }
        }
        return $routes;
    }


    public function createNamespace($file, $directory)
    {
        $rootNamespace = '\\App\\Mock\\Interfaces\\';
        $path = $file->getPathName();
        preg_match('/Interfaces\/(.*?)\.php/', $path, $matches);

        if (isset($matches[1])) {
            $dataBetween = $matches[1];
        }
        $namespace = str_replace([$directory, '/'], ['', '\\'], $dataBetween);
        return $rootNamespace . $namespace;
    }


    public function createDataUsingInterface(Request $request, $interface)
    {
        $route = Route::current();
        $routePattern = $route->uri();
        $method = Str::lower($request->method());
        $functions = $this->getRoutes();
        $data = [];
        foreach ($functions[$routePattern] as $function) {
            $function_method = Str::lower($function['method']);
            if ($method == $function_method) {
                $data = $this->getQueryData($function['return']);
                break;
            }
        }
        return $data;
    }


    public function paginate($data)
    {
        return [
            "data" => $data,
            "current_page" => 1,
            "per_page" => 10,
            "total" => 100,
            "last_page" => 10
        ];
    }
}
