<?php

namespace G4T\MockInterface\Mock;

use Illuminate\Http\Request;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use Illuminate\Support\Facades\Route;

class Mock
{

    public function index(Request $request)
    {
        $route = Route::current();
        $routePattern = $route->uri();
        [$controller, $function] = config("interfaces.$routePattern");
        $factory = DocBlockFactory::createInstance();
        $class = new ReflectionClass($controller);
        $method = $class->getMethod($function);
        $docBlock = $factory->create($method->getDocComment());
        $returnAnnotation = $docBlock->getTagsByName('return');
        $returnAnnotation = $returnAnnotation[0]->getDescription();
        $schema = (string)$returnAnnotation;
        if (preg_match('/\[.*\]/', $schema, $matches)) {
            $extractedData = trim($matches[0], '[]');
            if (empty($extractedData)) {
                $type = 'all';
                $schema = str_replace('[]', '', $schema);
            } else {
                if($extractedData == 'paginate') {
                    $type = 'paginate';
                } else if($extractedData == 'simplePaginate') {
                    $type = 'simplePaginate';
                } else {
                    return "type not found";
                }
                $schema = str_replace("[$extractedData]", '', $schema);
            }
        } else {
            $type = 'single';
        }
        

        $schema = new $schema;


        if($type == 'paginate') {
            $data = $schema->count(10)->make();
            return $this->paginate($data);
        } else if($type == 'all') {
            return $schema->count(10)->make();
        } else if($type == 'simplePaginate') {
            return $schema->make();
        } else if($type == 'single') {
            return $schema->make();
        }

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
