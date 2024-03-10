<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Fielder;
use SilverStripe\Core\Extension;

class FieldListExtension extends Extension
{
    protected $fielder;
    protected $fielderSettings;

    public function initFielder($parent)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $caller = $backtrace[4]['function'];

        $allowedFns = ['updateCMSFields', 'getCMSFields', 'updateSettingsFields', 'getSettingsFields'];

        if (in_array($caller, $allowedFns)) {

            if ($caller == 'getCMSFields' || $caller == 'updateCMSFields') {
                if ($this->fielder && get_class($this->fielder->getParentObject()) === get_class($parent)) {
                    return $this->fielder->getFields();
                }

                $this->fielder = new Fielder($this->owner, $parent);

                return $this->fielder->getFields();
            } else {

                if ($this->fielderSettings && get_class($this->fielderSettings->getParentObject()) === get_class($parent)) {
                    return $this->fielderSettings->getFields();
                }

                $this->fielderSettings = new Fielder($this->owner, $parent);

                return $this->fielderSettings->getFields();
            }
        }
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
}
