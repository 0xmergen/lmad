<?php

declare(strict_types=1);

namespace Lmad\Mcp\Schema;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;
use Lmad\Support\ReflectionHelper;

/**
 * Parses Laravel routes.
 *
 * Extracts and normalizes route information (URI, methods, middleware,
 * controller details, etc.).
 */
final class RouteParser
{
    /**
     * Parses a Route object into array format.
     *
     * Returns URI, HTTP methods, middleware, controller information,
     * and other route properties.
     *
     * @param  Route  $route  Laravel Route object
     * @return array{uri: string, methods: array, name: string|null, domain: string, middleware: array, parameters: array, wheres: array, controller: array, is_api: bool}
     */
    public function parse(Route $route): array
    {
        $action = $route->getAction();
        $controllerInfo = $this->parseControllerInfo($action);

        return [
            'uri' => $route->uri,
            'methods' => $route->methods,
            'name' => $route->getName(),
            'domain' => $route->getDomain(),
            'middleware' => array_values($route->middleware()),
            'parameters' => $route->parameterNames(),
            'wheres' => $route->wheres,
            'controller' => $controllerInfo,
            'is_api' => $this->isApiRoute($route),
        ];
    }

    /**
     * Finds and parses a route by URI and HTTP method.
     *
     * @param  string  $uri  Route URI (e.g., "api/users")
     * @param  string  $method  HTTP method (e.g., "GET", "POST")
     * @return array|null Route information or null if not found
     */
    public function parseByUriAndMethod(string $uri, string $method): ?array
    {
        $routes = collect(RouteFacade::getRoutes()->getRoutes())->flatten()
            ->first(fn (Route $route) => $route->uri === $uri && in_array($method, $route->methods, true));

        if (! $routes) {
            return null;
        }

        return $this->parse($routes);
    }

    /**
     * Extracts controller information from the route action array.
     *
     * Extracts controller and method from action string and adds file location information.
     *
     * @param  array  $action  Route action array
     * @return array{class: string|null, method: string|null, file_path: string|null|false, start_line: int|false|null, type: string}
     */
    private function parseControllerInfo(array $action): array
    {
        $info = [
            'class' => null,
            'method' => null,
            'file_path' => null,
            'start_line' => null,
            'type' => 'closure',
        ];

        if (isset($action['controller']) && is_string($action['controller'])) {
            $parsed = $this->parseActionString($action['controller']);
            $info = array_merge($info, $parsed);
            $info['type'] = 'controller';
        } elseif (isset($action['uses']) && is_string($action['uses'])) {
            if (str_contains($action['uses'], '@')) {
                $parsed = $this->parseActionString($action['uses']);
                $info = array_merge($info, $parsed);
                $info['type'] = 'controller';
            }
        }

        return $info;
    }

    /**
     * Parses a controller@class@method string into components.
     *
     * Extracts class, method, and file location information.
     *
     * @param  string  $actionString  Action string in "Class@method" format
     * @return array{class: string|null, method: string|null, file_path: string|null|false, start_line: int|false|null}
     */
    private function parseActionString(string $actionString): array
    {
        $parts = explode('@', $actionString);
        $class = $parts[0] ?? null;
        $method = $parts[1] ?? null;

        if (! $class || ! $method) {
            return [
                'class' => null,
                'method' => null,
                'file_path' => null,
                'start_line' => null,
            ];
        }

        return [
            'class' => $class,
            'method' => $method,
            'file_path' => ReflectionHelper::getClassFileName($class),
            'start_line' => ReflectionHelper::getMethodStartLine($class, $method),
        ];
    }

    /**
     * Checks if the route is an API route.
     *
     * Determines by the presence of API middleware or URI prefix.
     *
     * @param  Route  $route  Laravel Route object
     * @return bool True if API route
     */
    private function isApiRoute(Route $route): bool
    {
        $middleware = $route->middleware();

        return in_array('api', $middleware, true)
            || collect($middleware)->contains(fn ($m) => str_starts_with($m, 'api:'))
            || str_starts_with($route->uri, 'api/');
    }
}
