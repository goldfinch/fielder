<?php

namespace Goldfinch\Fielder;

use Closure;
use Goldfinch\Illuminate\Validator as IlluminateValidator;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\ORM\FieldType\DBHTMLText;

class Validator
{
    private static $validation;
    private static $parent;

    public function __construct($parent, ValidationResult $validation)
    {
        self::$parent = $parent;
        self::$validation = $validation;
    }

    public static function validateCommon($rules, $result): ValidationResult
    {
        $parent = self::$parent;

        $validator = IlluminateValidator::validate($parent->toMap(), $rules, [], [], false);

        if (is_array($validator)) {

            foreach ($validator as $field => $messages) {

                $str = '';

                foreach ($messages as $message) {
                    if ($str != '') {
                        $str .= PHP_EOL;
                    }

                    $str .= '- ' . $message;
                }

                // $result->addError($str);
                $result->addFieldError($field, $str);
            }
        }

        return $result;
    }

    public static function validateClosure($rules, $result): ValidationResult
    {
        $parent = self::$parent;

        foreach ($rules as $field => $closure) {

            $value = $parent->$field;
            $labels = $parent->fieldLabels();
            $fail = function($str) use ($field, $result, $labels) {

                if (isset($labels[$field])) {
                    $field = $labels[$field];
                }

                $str = str_replace(':attribute', '<strong>'.$field.'</strong>', $str);
                $strHTML = DBHTMLText::create();
                $result->addError($strHTML->setValue($str));
            };
            $closure->call($parent, $value, $fail);
        }

        return $result;
    }

    public static function validate(): ValidationResult
    {
        $result = self::$validation;
        $parent = self::$parent;

        $fielder = $parent->fielderFields($parent->getCMSFields());

        $closureRules = [];
        $commonRules = [];

        foreach ($fielder->getValidatorRules() as $field => $rule) {

            if ($rule instanceof Closure) {
                $closureRules[$field] = $rule;
            } else {
                $commonRules[$field] = $rule;
            }
        }

        if (!empty($closureRules)) {
            $result = self::validateClosure($closureRules, $result);
        }

        if (!empty($commonRules)) {
            $result = self::validateCommon($commonRules, $result);
        }

        return $result;
    }

    public static function create(...$args): static
    {
        return new static(...$args);
    }
}
