<?php

namespace Goldfinch\Harvest;

use SilverStripe\Forms\DateField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\FieldType\DBFloat;
use SilverStripe\ORM\FieldType\DBDouble;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;

class Harvest
{
    private $fields = null;

    private $parent = null;

    public function __construct(&$fields, $parent)
    {
        $this->fields = $fields;
        $this->parent = $parent;
    }

    public function field($name, $title = null)
    {
        return $this->parent->dbObject($name)->scaffoldFormField($title);
    }

    public function checkbox($name, $title = null, $value = null)
    {
        return CheckboxField::create($name, $title, $value);
    }

    public function currency($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return CurrencyField::create($name, $title, $value, $maxLength, $form);
    }

    public function date($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return DateField::create($name, $title, $value, $maxLength, $form);
    }

    public function datetime($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return DatetimeField::create($name, $title, $value, $maxLength, $form);
    }

    public function html($name, $title = null, $value = '', $config = null)
    {
        return HTMLEditorField::create($name, $title, $value, $config);
    }







    public function decimal($name, $title = null, $wholeSize = 9, $decimalSize = 2, $defaultValue = 0)
    {
        // public function decimal($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(2);

        $field = new DBDecimal($name, $wholeSize, $decimalSize, $defaultValue);
        return $field->scaffoldFormField($title);
    }

    public function double($name, $title = null, $defaultVal = 0)
    {
        // public function double($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form);

        $field = new DBDouble($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    public function float($name, $title = null, $defaultVal = 0)
    {
        // public function float($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(null);

        $field = new DBFloat($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    public function enum($name, $title = null, $source = [], $value = null)
    {
        // public function enum($name, $title = null, $enum = null, $default = 0, $options = [])
        // $field = new DBEnum($name, $enum, $default, $options);
        // return $field->scaffoldFormField($title);
        // return DropdownField::create($name, $title, $source, $value);

        return $this->field($name);
    }

    public function htmlText($name)
    {
        return $this->field($name);
    }

    public function htmlFragment($name)
    {
        return $this->field($name);
    }

    public function htmlVarchar($name)
    {
        return $this->field($name);
    }

    // public function bigInt($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }

    // public function ssss($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return SSS::create($name, $title, $value, $maxLength, $form)
    // }
}
