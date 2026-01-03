<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;
use App\Core\Response;

/**
 * Base Controller
 *
 * Provides common functionality for all controllers
 */
abstract class BaseController
{
    /**
     * Validate request data against rules
     *
     * @param Request $request
     * @param array $rules ['field' => 'required|type|...']
     * @return array|null Validation errors or null if valid
     */
    protected function validate(Request $request, array $rules): ?array
    {
        $errors = [];
        $data = array_merge($request->getQuery(), $request->getBody());

        foreach ($rules as $field => $ruleString) {
            $fieldRules = explode('|', $ruleString);
            $value = $data[$field] ?? null;

            foreach ($fieldRules as $rule) {
                // Required validation
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = "$field is required";
                }

                // Type validations
                if ($value !== null) {
                    if ($rule === 'string' && !is_string($value)) {
                        $errors[$field][] = "$field must be a string";
                    }
                    if ($rule === 'int' && !is_numeric($value)) {
                        $errors[$field][] = "$field must be an integer";
                    }
                    if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = "$field must be a valid email";
                    }
                }
            }
        }

        return empty($errors) ? null : $errors;
    }

    /**
     * Get route parameter
     */
    protected function getRouteParam(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->routeParams[$key] ?? $default;
    }
}