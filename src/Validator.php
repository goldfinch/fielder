<?php

namespace Goldfinch\Fielder;

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

    public static function validate(): ValidationResult
    {
        $result = self::$validation;
        $parent = self::$parent;

        $fielder = $parent->fielderFields($parent->getCMSFields());

        foreach ($fielder->getValidatorRules() as $field => $closure) {
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

    public static function create(...$args): static
    {
        return new static(...$args);
    }
}
