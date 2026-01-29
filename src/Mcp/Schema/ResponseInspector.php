<?php

declare(strict_types=1);

namespace Lmad\Mcp\Schema;

use Lmad\Support\ReflectionHelper;

/**
 * Inspects controller method return types.
 *
 * Analyzes what the method returns (JsonResource, Model, Response, etc.)
 * and file location information.
 */
final class ResponseInspector
{
    /**
     * Inspects the controller method's return type information.
     *
     * @param  string  $controllerClass  Controller class name
     * @param  string  $method  Method name
     * @return array{controller: string, method: string, return_type: string|null, file: string|null|false, start_line: int|false}
     */
    public function inspect(string $controllerClass, string $method): array
    {
        $returnType = ReflectionHelper::getMethodReturnType($controllerClass, $method);
        $fileName = ReflectionHelper::getClassFileName($controllerClass);
        $startLine = ReflectionHelper::getMethodStartLine($controllerClass, $method);

        return [
            'controller' => $controllerClass,
            'method' => $method,
            'return_type' => $returnType,
            'file' => $fileName,
            'start_line' => $startLine,
        ];
    }

    /**
     * Returns the file location information of the controller method.
     *
     * Includes start and end line numbers.
     *
     * @param  string  $controllerClass  Controller class name
     * @param  string  $method  Method name
     * @return array{file: string|null|false, start_line: int|false, end_line: int|null}
     */
    public function getMethodLocation(string $controllerClass, string $method): array
    {
        $fileName = ReflectionHelper::getClassFileName($controllerClass);
        $startLine = ReflectionHelper::getMethodStartLine($controllerClass, $method);

        try {
            $reflection = new \ReflectionMethod($controllerClass, $method);
            $endLine = $reflection->getEndLine();
        } catch (\ReflectionException $e) {
            $endLine = null;
        }

        return [
            'file' => $fileName,
            'start_line' => $startLine,
            'end_line' => $endLine,
        ];
    }
}
