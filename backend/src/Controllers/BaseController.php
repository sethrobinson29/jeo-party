<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Request;

abstract class BaseController
{
    protected function validate(Request $request, array $rules): ?array
    {
        $errors = [];
        $data = array_merge($request->getQuery(), $request->getBody());

        foreach ($rules as $field => $ruleString) {
            $value = $data[$field] ?? null;

            foreach (explode('|', $ruleString) as $rule) {
                if ($rule === 'required' && ($value === null || $value === '')) {
                    $errors[$field][] = "$field is required";
                }

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

    protected function getRouteParam(Request $request, string $key, mixed $default = null): mixed
    {
        return $request->getRouteParams()[$key] ?? $default;
    }
}
