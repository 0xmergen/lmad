<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Lmad\Mcp\LmadServer;
use Laravel\Mcp\Facades\Mcp;

// Local MCP server for LMAD API Discovery
Mcp::local('lmad', LmadServer::class);
