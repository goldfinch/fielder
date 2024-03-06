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
    private static $validation;
    private static $parent;
    private static $extension;

    public function __construct($parent, ValidationResult $validation)
    {
        self::$validation = $validation;

        if (is_subclass_of($parent, Extension::class)) {
            self::$extension = $parent;
            self::$parent = $parent->getOwner();
        } else {
            self::$parent = $parent;
        }
    }

    public static function validateCommon($rules, $result): ValidationResult
    {
        $parent = self::$parent;
        // $data = $parent->toMap();

        foreach($rules as $n => $r) {
            $data[$n] = $parent->$n;
        }

        $validator = IlluminateValidator::validate($data, $rules, [], [], false);

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

        if (in_array(get_class($parent), [
            ChangeSetItem::class,
            ChangeSet::class,
        ])) {
            return $result;
        }

        $fielder = $parent->getCMSFields()->getFielder();
        // $fielder = $parent->fielderFields($parent->getCMSFields());

        $closureRules = [];
        $commonRules = [];

        self::rulesBucket($fielder, $closureRules, $commonRules);

        // settings fields
        if (method_exists($parent, 'getSettingsFields') || method_exists($parent, 'updateSettingsFields')) { //
            $fielder = $parent->getSettingsFields()->getFielder();
        }

        if (!empty($closureRules)) {
            $result = self::validateClosure($closureRules, $result);
        }

        if (!empty($commonRules)) {
            $result = self::validateCommon($commonRules, $result);
        }

        return $result;
    }

    protected static function rulesBucket(&$fielder, &$closureRules, &$commonRules)
    {
        foreach ($fielder->getValidatorRules() as $field => $rule) {

            if ($rule instanceof Closure) {
                $closureRules[$field] = $rule;
            } else {
                $commonRules[$field] = $rule;
            }
        }
    }

    public static function create(...$args): static
    {
        return new static(...$args);
    }
}
