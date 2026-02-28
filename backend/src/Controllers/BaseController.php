<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

abstract class BaseController
{
    protected function getRouteParam(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->getRouteParams()[$key] ?? $default;
    }
}
