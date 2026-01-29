# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Added
- Initial release of LMAD - Laravel MCP API Discovery
- **MCP Tools (5)**:
  - `list_api_routes` - List all API routes with optional filters
  - `get_route_details` - Get detailed route information including controller, middleware, and file locations
  - `get_request_rules` - Parse FormRequest validation rules, custom error messages, and authorization logic
  - `get_response_schema` - Analyze controller return types (JsonResource, Model, array, JsonResponse)
  - `analyze_endpoint` - Complete endpoint analysis combining route, controller, request, and response information
- **MCP Resources (2)**:
  - `api_routes` - Dynamic access to API routes via `route://{uri}` URI template
  - `controller` - Controller and method information via `controller://{class}/{method?}` URI template
- **HTTP API Endpoints** - Web-based endpoints for browser testing and debugging
- **Laravel 12 Compatibility** - Uses `RouteFacade::getRoutes()->getRoutes()->flatten()` for route discovery
- **Reflection-based Analysis** - Controller, FormRequest, and JsonResource inspection via PHP reflection
- **Schema Parsers** - RouteParser, ControllerInspector, RequestInspector, ResponseInspector classes
- **Comprehensive Test Suite** - Pest tests for all MCP tools and services
