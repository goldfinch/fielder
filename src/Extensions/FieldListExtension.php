<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Fielder;
use SilverStripe\Core\Extension;
use SilverStripe\Forms\FieldList;

class FieldListExtension extends Extension
{
    protected $fielder;
    protected $fielderSettings;

    // protected $fielderType;

    public function initFielder($parent)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $caller = $backtrace[4]['function'];

        $allowedFns = ['updateCMSFields', 'getCMSFields', 'updateSettingsFields', 'getSettingsFields'];
        // dump(0, get_class($this->owner), get_class($parent), $this->fielder->getParentObject());

        if (in_array($caller, $allowedFns)) {

            if ($caller == 'getCMSFields' || $caller == 'updateCMSFields') {
                // dump(1);
                if ($this->fielder && get_class($this->fielder->getParentObject()) === get_class($parent)) {
                // dump(2);
                return $this->fielder->getFields(); // [$this->fielder->getFields(), $this->fielder];
                }

                $this->fielder = new Fielder($this->owner, $parent);

                return $this->fielder->getFields(); // [$this->fielder->getFields(), $this->fielder];
            } else {
                // dump(3);

                if ($this->fielderSettings && get_class($this->fielderSettings->getParentObject()) === get_class($parent)) {
                // dump(4);
                return $this->fielderSettings->getFields(); // [$this->fielder->getFields(), $this->fielder];
                }

                $this->fielderSettings = new Fielder($this->owner, $parent);

                return $this->fielderSettings->getFields(); // [$this->fielder->getFields(), $this->fielder];
            }
        }
    }

    public function fielder($parent)
    {

        if ($this->fielder) {
            return $this->fielder;
        }


        $this->fielder = new Fielder($this->owner, $parent);

        return $this->fielder;

        // // $this->fielderType = debug_backtrace()[4]['function'];

        // if (get_class($this) == FieldList::class) {
        //     $fieldList = $this;
        // } else if ($this->owner && get_class($this->owner) == FieldList::class) {
        //     $fieldList = $this->owner;
        // }

        // if (isset($fieldList)) {
        //     $this->fielder = new Fielder($fieldList, $parent);
        // }

        // return $this->fielder;
    }

    public function getFielder()
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $caller = $backtrace[4]['function'];

        $allowedFns = ['updateCMSFields', 'getCMSFields', 'updateSettingsFields', 'getSettingsFields'];

        if (in_array($caller, $allowedFns)) {

            if ($caller == 'getCMSFields' || $caller == 'updateCMSFields') {
                return $this->fielder;
            } else {
                return $this->fielderSettings;
            }
        }
        else
        {
            return [$this->fielder, $this->fielderSettings];
        }
    }

    public function setFielder()
    {
        return $this->fielder;
    }

    // public function getFielderType()
    // {
    //     return $this->fielderType;
    // }
}
