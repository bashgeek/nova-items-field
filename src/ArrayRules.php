<?php

namespace NovaItemsField;

use Closure;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ArrayRules implements Rule
{
    public $rules = [];

    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    protected function getValidationAttribute($attribute)
    {
        return str_replace('.', '=>', $attribute);
    }

    protected function getErrorAttribute($validationAttribute, $errorAttribute)
    {
        return preg_replace(
            '/'.$validationAttribute.'\.?/',
            '',
            str_replace('=>', '.', $errorAttribute)
        );
    }

    protected function getRules($attribute): array
    {
        $rules = [];
        foreach ($this->rules as $attr => $rule) {
            if (empty($rule)) {
                continue;
            }
            if ($attr === $attribute.'.*') {
                $rules[$attr] = $rule;
                continue;
            }
            if (empty($attr) || $attr === $attribute) {
                $rules[$attribute] = $rule;
                continue;
            }
            if (strpos($attr, $attribute.'.') === 0) {
                continue;
            }

            $rules[$attribute.'.'.$attr] = $rule;
        }
        return $rules;
    }

    public function validate(string $attribute, $value, Closure $fail): void
    {
        $validationAttribute = $this->getValidationAttribute($attribute);
        $input = [$validationAttribute => json_decode($value)];
        $rules =  $this->getRules($validationAttribute);
        $validator = Validator::make($input, $rules, [], [(string)($validationAttribute) => 'list', $validationAttribute . '.*' => 'input']);
        $errors = [];
        foreach ($validator->errors()->toArray() as $attr => $error) {
            $errors[$this->getErrorAttribute($attribute, $attr)] = $error;
        }
        if (count($errors)) {
            $fail(json_encode($errors));
        }
    }
}
