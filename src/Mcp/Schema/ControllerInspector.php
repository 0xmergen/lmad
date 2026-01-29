<?php

declare(strict_types=1);

namespace Lmad\Mcp\Schema;

use Lmad\Support\ReflectionHelper;

/**
 * Inspects controller classes and methods.
 *
 * Analyzes controller information, method parameters, return types,
 * and used classes (use statements).
 */
final class ControllerInspector
{
    /**
     * Inspects controller class and method information.
     *
     * @param  string  $controllerClass  Fully namespaced controller class name
     * @param  string  $method  Method name to inspect
     * @return array{class: string, method: string, file_path: string|null|false, start_line: int|false, return_type: string|null, parameters: array, uses: array}|array{error: string}
     */
    public function inspect(string $controllerClass, string $method): array
    {
        if (! class_exists($controllerClass)) {
            return [
                'error' => "Controller class '{$controllerClass}' does not exist.",
            ];
        }

        if (! ReflectionHelper::methodExists($controllerClass, $method)) {
            return [
                'error' => "Method '{$method}' does not exist in controller '{$controllerClass}'.",
            ];
        }

        return [
            'class' => $controllerClass,
            'method' => $method,
            'file_path' => ReflectionHelper::getClassFileName($controllerClass),
            'start_line' => ReflectionHelper::getMethodStartLine($controllerClass, $method),
            'return_type' => ReflectionHelper::getMethodReturnType($controllerClass, $method),
            'parameters' => ReflectionHelper::getMethodParameters($controllerClass, $method),
            'uses' => $this->extractUses($controllerClass),
        ];
    }

    /**
     * Finds the FormRequest class used in the controller method.
     *
     * Returns the first class that extends FormRequest among method parameters.
     *
     * @param  string  $controllerClass  Controller class name
     * @param  string  $method  Method name
     * @return string|null FormRequest class name or null
     */
    public function getRequestClass(string $controllerClass, string $method): ?string
    {
        $parameters = ReflectionHelper::getMethodParameters($controllerClass, $method);

        foreach ($parameters as $param) {
            $type = $param['type'] ?? null;

            if ($type && $this->isFormRequest($type)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Checks if the controller method's return type has JsonResource.
     *
     * @param  string  $controllerClass  Controller class name
     * @param  string  $method  Method name
     * @return string|null JsonResource class name or null
     */
    public function getResourceClass(string $controllerClass, string $method): ?string
    {
        $returnType = ReflectionHelper::getMethodReturnType($controllerClass, $method);

        if ($returnType && $this->isJsonResource($returnType)) {
            return $returnType;
        }

        return null;
    }

    /**
     * Finds the Model parameter used in the controller method.
     *
     * Returns the first class that extends Eloquent Model among method parameters
     * (typically for route model binding).
     *
     * @param  string  $controllerClass  Controller class name
     * @param  string  $method  Method name
     * @return string|null Model class name or null
     */
    public function getModelClass(string $controllerClass, string $method): ?string
    {
        $parameters = ReflectionHelper::getMethodParameters($controllerClass, $method);

        foreach ($parameters as $param) {
            $type = $param['type'] ?? null;

            if ($type && $this->isModel($type)) {
                return $type;
            }
        }

        return null;
    }

    /**
     * Extracts use statements from the controller class.
     *
     * Filters out Illuminate namespace classes.
     *
     * @param  string  $controllerClass  Controller class name
     * @return array Used class names
     */
    private function extractUses(string $controllerClass): array
    {
        $uses = ReflectionHelper::getClassUses($controllerClass);

        return array_filter($uses, fn ($use) => ! str_starts_with($use, 'Illuminate\\'));
    }

    /**
     * Checks if the given class is a FormRequest.
     *
     * @param  string  $type  Class name
     * @return bool True if FormRequest
     */
    private function isFormRequest(string $type): bool
    {
        if (! class_exists($type)) {
            return false;
        }

        $parents = class_parents($type);

        return in_array('Illuminate\\Foundation\\Http\\FormRequest', $parents, true);
    }

    /**
     * Checks if the given class is a JsonResource.
     *
     * @param  string  $type  Class name
     * @return bool True if JsonResource
     */
    private function isJsonResource(string $type): bool
    {
        if (! class_exists($type)) {
            return false;
        }

        $parents = class_parents($type);

        return in_array('Illuminate\\Http\\Resources\\Json\\JsonResource', $parents, true)
            || in_array('Illuminate\\Http\\Resources\\Json\\ResourceCollection', $parents, true);
    }

    /**
     * Checks if the given class is an Eloquent Model.
     *
     * @param  string  $type  Class name
     * @return bool True if Model
     */
    private function isModel(string $type): bool
    {
        if (! class_exists($type)) {
            return false;
        }

        $parents = class_parents($type);

        return in_array('Illuminate\\Database\\Eloquent\\Model', $parents, true);
    }
}
