<?php

namespace NovaItemsField;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class ArrayRules implements Rule
{
    public $rules = [];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Replaces "." symbol in attribute name to avoid
     * Validator misinterpretation as nested array element
     * @param string $attribute
     * @return string sanitized attribute
     */
    protected function getValidationAttribute($attribute)
    {
        return str_replace('.', '->', $attribute);
    }

    /**
     * Restore sanitized "." symbol in attribute name and
     * removes the attribute prefix
     * @param string $validationAttribute
     * @param string $errorAttribute
     * @return string sanitized error attribute
     */
    protected function getErrorAttribute($validationAttribute, $errorAttribute)
    {
        return preg_replace(
            '/'.$validationAttribute.'\.?/',
            '',
            str_replace('->', '.', $errorAttribute)
        );
    }

    /**
     * Get the rules, appends the attribute name if it's not
     * already defined as prefix in the rule attribute
     * @param string $attribute
     * @return array The rules array
     */
    protected function getRules($attribute)
    {
        $rules = [];
        foreach ($this->rules as $attr => $rule) {
            if (empty($rule)) {
                continue;
            }
            if (empty($attr)) {
                $rules[$attribute] = $rule;
                continue;
            }
            if (str_starts_with($attr, $attribute.'.')) {
                continue;
            }

            $rules[$attribute.'.'.$attr] = $rule;
        }
        return $rules;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $validationAttribute = $this->getValidationAttribute($attribute);
        $input = [$validationAttribute => json_decode($value)];
        $validator = Validator::make($input, $this->getRules($validationAttribute), [], ["{$validationAttribute}" => 'list', "{$validationAttribute}.*" => 'input']);
        $errors = [];
        foreach ($validator->errors()->toArray() as $attr => $error) {
            $errors[$this->getErrorAttribute($attribute, $attr)] = $error;
        }
        $this->message = json_encode($errors);
        return $validator->passes();
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
