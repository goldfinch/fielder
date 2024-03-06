<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Fielder;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class FieldListExtension extends Extension
{
    protected $fielder;
    // protected $fielderType;

    public function fielder($parent)
    {
        // getSettingsFields
        // updateSettingsFields

        if ($this->fielder) {
            return $this->fielder;
        }

        // $this->fielderType = debug_backtrace()[4]['function'];

        if (get_class($this) == FieldList::class) {
            $fieldList = $this;
        } else if ($this->owner && get_class($this->owner) == FieldList::class) {
            $fieldList = $this->owner;
        }

        if (isset($fieldList)) {
            $this->fielder = new Fielder($fieldList, $parent);
        }

        return $this->fielder;
    }

    // public function getFielderType()
    // {
    //     return $this->fielderType;
    // }

    public function getFielder()
    {
        return $this->fielder;
    }
}
