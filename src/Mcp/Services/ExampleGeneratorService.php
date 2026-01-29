<?php

declare(strict_types=1);

namespace Lmad\Mcp\Services;

use Illuminate\Support\Str;
use Lmad\Mcp\Schema\RequestInspector;
use Lmad\Support\ReflectionHelper;

/**
 * Service for generating example request/response for API endpoints.
 *
 * Creates example request body and response information based on
 * FormRequest rules.
 */
final class ExampleGeneratorService
{
    public function __construct(
        private readonly RequestInspector $requestInspector,
    ) {}

    /**
     * Generates an example request/response for an endpoint.
     *
     * Identifies required fields from FormRequest rules and creates
     * appropriate example values.
     *
     * @param  string  $uri  Endpoint URI
     * @param  string  $method  HTTP method
     * @param  string  $controllerClass  Controller class name
     * @param  string  $controllerMethod  Controller method name
     * @param  string|null  $requestClass  FormRequest class name (if any)
     * @return array{http_method: string, uri: string, request_body?: array, expected_response_type?: string|null}
     */
    public function generate(
        string $uri,
        string $method,
        string $controllerClass,
        string $controllerMethod,
        ?string $requestClass,
    ): array {
        $example = [
            'http_method' => $method,
            'uri' => $uri,
        ];

        if ($requestClass) {
            $rules = $this->requestInspector->extractRules($requestClass);
            $example['request_body'] = $this->generateRequestBody($rules);
        }

        $returnType = ReflectionHelper::getMethodReturnType($controllerClass, $controllerMethod);

        if ($returnType) {
            $example['expected_response_type'] = $returnType;
        }

        return $example;
    }

    /**
     * Generates an example request body for required fields.
     *
     * @param  array  $rules  Validation rules
     * @return array Example request body
     */
    private function generateRequestBody(array $rules): array
    {
        return collect($rules)
            ->filter(fn ($fieldRules) => is_array($fieldRules))
            ->filter(fn ($fieldRules) => $this->isRequired($fieldRules))
            ->map(fn ($fieldRules, $field) => $this->guessValue($field, $fieldRules))
            ->all();
    }

    /**
     * Checks if a field is required.
     *
     * @param  array  $fieldRules  Field rules
     * @return bool True if required
     */
    private function isRequired(array $fieldRules): bool
    {
        return collect($fieldRules)->contains(
            fn ($rule) => isset($rule['rule']) && $rule['rule'] === 'required'
        );
    }

    /**
     * Guesses a value for a field based on its rules.
     *
     * Finds the first applicable rule, or uses a default value.
     *
     * @param  string  $field  Field name
     * @param  array  $rules  Field rules
     * @return mixed Guessed value
     */
    private function guessValue(string $field, array $rules): mixed
    {
        return collect($rules)
            ->filter(fn ($rule) => isset($rule['rule']))
            ->map(fn ($rule) => $rule['rule'])
            ->first(fn ($rule) => $rule !== 'required')
            ?? $this->defaultValue($field);
    }

    /**
     * Returns a default value based on the field name.
     *
     * Generates appropriate values based on keywords found in the field
     * name (email, password, date, etc.).
     *
     * @param  string  $field  Field name
     * @return mixed Default value
     */
    private function defaultValue(string $field): mixed
    {
        return match (true) {
            Str::contains($field, 'email') => 'example@example.com',
            Str::contains($field, ['url', 'link', 'website']) => 'https://example.com',
            Str::contains($field, ['password', 'secret']) => 'password123',
            Str::contains($field, 'phone') => '+1234567890',
            Str::contains($field, 'id') => 1,
            Str::contains($field, ['price', 'amount', 'total']) => 99.99,
            Str::contains($field, ['count', 'quantity', 'number']) => 1,
            Str::contains($field, ['active', 'enabled', 'verified']) => true,
            Str::contains($field, ['date', 'time']) => now()->toDateString(),
            Str::contains($field, 'name') => 'Example Name',
            Str::contains($field, ['title', 'subject']) => 'Example Title',
            Str::contains($field, ['description', 'content', 'body']) => 'Example description text',
            Str::contains($field, ['address', 'city', 'country']) => 'Example Value',
            default => 'value',
        };
    }
}
