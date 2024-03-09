<?php

namespace Goldfinch\Fielder;

use Closure;
use SilverStripe\Core\Extension;
use SilverStripe\Versioned\ChangeSet;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Versioned\ChangeSetItem;
use SilverStripe\ORM\FieldType\DBHTMLText;
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

        // return $result;
    }

    public static function addError($field, $str)
    {
        self::$result['errors'][$field][] = $str;
    }

    public static function validateClosure($rules)
    {
        foreach ($rules as $field => $closure) {

            // $value = $parent->$field;
            // $labels = $parent->fieldLabels();
            // $fail = function($str) use ($field, $result, $labels) {

            //     if (isset($labels[$field])) {
            //         $field = $labels[$field];
            //     }

            //     $str = str_replace(':attribute', '<strong>'.$field.'</strong>', $str);
            //     $strHTML = DBHTMLText::create();
            //     $result->addError($strHTML->setValue($str));
            // };
            // $closure->call($parent, $value, $fail);
        }

        // return $result;
    }

    public static function validate()
    {
        $data = self::$data;
        $fielder = self::$fielder;

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
