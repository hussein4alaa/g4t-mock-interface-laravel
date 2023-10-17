<?php

namespace G4T\MockInterface\Helpers;

use App\Mock\Interfaces\UserInterface;
use phpDocumentor\Reflection\DocBlockFactory;
use ReflectionClass;
use ReflectionMethod;

trait Helper
{
    public function getFunctionsFromInterface($interface)
    {
        $reflectionClass = new ReflectionClass($interface);
        $methods = $reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC);
        $response = [];
        foreach ($methods as $method) {
            $response = $this->getAnnotationsFromFunction($method, $response);
        }
        return $response;
    }


    public function getAnnotationsFromFunction($function, &$response = [])
    {
        try {
            $factory = DocBlockFactory::createInstance();
            $class = $function->class;
            $class = new ReflectionClass($class);

            $method = $class->getMethod($function->name);

            $docBlock = $factory->create($method->getDocComment());
            $returnAnnotation = $docBlock->getTagsByName('return');
            $returnAnnotation = (string)$returnAnnotation[0]->getDescription();

            $methodAnnotation = $docBlock->getTagsByName('method');
            $methodAnnotation = (string)$methodAnnotation[0]->getMethodName();

            $routeAnnotation = $docBlock->getTagsByName('route');
            $routeAnnotation = (string)$routeAnnotation[0]->getDescription();

            $response[$routeAnnotation][] = [
                'return' => $returnAnnotation,
                'method' => $methodAnnotation,
                'route' => $routeAnnotation,
            ];
            return $response;
        } catch (\Throwable $th) {
        }
    }




    public function getQueryData($schema)
    {
        if (preg_match('/\[.*\]/', $schema, $matches)) {
            $extractedData = trim($matches[0], '[]');
            if (empty($extractedData)) {
                $type = 'all';
                $schema = str_replace('[]', '', $schema);
            } else {
                if ($extractedData == 'paginate') {
                    $type = 'paginate';
                } else if ($extractedData == 'simplePaginate') {
                    $type = 'simplePaginate';
                } else {
                    return "type not found";
                }
                $schema = str_replace("[$extractedData]", '', $schema);
            }
        } else {
            $type = 'single';
        }
        return $this->getData($type, $schema);
    }


    public function getData($type, $schema)
    {
        $schema = new $schema;

        if ($type == 'paginate') {
            $data = $schema->count(10)->make();
            return $this->paginate($data);
        } else if ($type == 'all') {
            return $schema->count(10)->make();
        } else if ($type == 'simplePaginate') {
            return $schema->make();
        } else if ($type == 'single') {
            return $schema->make();
        }
    }
}
