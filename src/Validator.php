<?php

namespace Goldfinch\Fielder;

use Closure;
use SilverStripe\ORM\ValidationResult;
use Goldfinch\Illuminate\Validator as IlluminateValidator;

class Validator
{
    private static $data;
    private static $fielder;

    private static $result;

    public function __construct($data, $fielder)
    {
        self::$data = $data;
        self::$fielder = $fielder;
    }

    public static function validateCommon($rules)
    {
        // $parent = self::$parent;
        // $data = [];

        // foreach($rules as $n => $r) {
        //     if (isset(self::$data[$n])) {
        //         $data[$n] = self::$data[$n];
        //     }
        // }

        $validator = IlluminateValidator::validate(self::$data, $rules, [], [], false);

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
                // $result->addFieldError($field, $str);
                self::addError($field, $str);
            }
        }
    }

    public static function addError($field, $str)
    {
        self::$result['errors'][$field][] = $str;
    }

    public static function validateClosure($rules)
    {
        $result = ValidationResult::create();

        foreach ($rules as $field => $closures) {

            foreach ($closures as $closure) {

                $value = self::$data[$field];
                $label = self::$fielder->dataField($field)->Title();

                $fail = function($str) use ($field, $result, $label) {

                    $str = str_replace(':attribute', '<strong>'.$label.'</strong>', $str);
                    $result->addFieldError($field, $str);
                };

                $closure->call($result, $value, $fail);
            }
        }

        $errors = $result->getMessages();

        if (count($errors)) {
            foreach ($errors as $error) {
                self::addError($error['fieldName'], $error['message']);
            }
        }
    }

    public static function validate()
    {
        $data = self::$data;
        $fielder = self::$fielder;

        $closureRules = [];
        $commonRules = [];

        foreach ($fielder->getValidatorRules() as $field => $rules) {

            foreach ($rules as $rule) {
                if ($rule instanceof Closure) {
                    $closureRules[$field][] = $rule;
                } else {
                    $commonRules[$field][] = $rule;
                }
            }
        }

        if (!empty($closureRules)) {
            self::validateClosure($closureRules);
        }

        if (!empty($commonRules)) {
            self::validateCommon($commonRules);
        }

        return self::$result;
    }

    public static function create(...$args): static
    {
        return new static(...$args);
    }
}
