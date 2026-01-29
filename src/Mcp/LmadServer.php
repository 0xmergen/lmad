<?php

declare(strict_types=1);

namespace Lmad\Mcp;

use Laravel\Mcp\Server;

/**
 * LMAD (Laravel MCP API Discovery) MCP Server definition.
 *
 * MCP server for discovering and analyzing API endpoints, routes,
 * controllers, FormRequest validation rules, and JsonResource response schemas.
 */
class LmadServer extends Server
{
    /** Server name */
    protected string $name = 'LMAD API Discovery';

    /** Server version */
    protected string $version = '1.0.0';

    /** Server description */
    protected string $description = 'Discovers and analyzes Laravel API endpoints, controllers, FormRequest validation rules, and JSON Resource response schemas for AI-assisted development.';

    /** Usage instructions */
    protected string $instructions = 'This server provides tools for discovering API routes, inspecting controller methods, analyzing FormRequest validation rules, and understanding JsonResource response schemas. Use it to get comprehensive information about Laravel API endpoints.';

    /**
     * MCP Tool classes.
     *
     * @var array<class-string>
     */
    protected array $tools = [
        Tools\ListApiRoutes::class,
        Tools\GetRouteDetails::class,
        Tools\GetRequestRules::class,
        Tools\GetResponseSchema::class,
        Tools\AnalyzeEndpoint::class,
    ];

    /**
     * MCP Resource classes.
     *
     * @var array<class-string>
     */
    protected array $resources = [
        Resources\ApiRoutesResource::class,
        Resources\ControllerResource::class,
    ];
}
