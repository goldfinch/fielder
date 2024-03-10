<?php

namespace Goldfinch\Fielder\Extensions;

use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\CompositeValidator;
use Goldfinch\Fielder\Forms\FielderValidator;
use Goldfinch\Fielder\Forms\FielderRequiredValidator;

class FielderExtension extends DataExtension
{
    protected $fieldsWithFielder = [];

    public function intFielder($fields)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 5);
        $caller = $backtrace[4]['function'];
        $allowedFns = ['updateCMSFields', 'getCMSFields', 'updateSettingsFields', 'getSettingsFields'];

        if (in_array($caller, $allowedFns)) {

            $fields->fielder($this);
            $this->fieldsWithFielder[get_class($this->owner)][$caller] = $fields;

            return $fields;
        }

        throw new Exception('Fielder can only be called in: ' . implode(', ', $allowedFns));
    }

    public function updateCMSCompositeValidator(CompositeValidator $compositeValidator): void
    {
         // FYI: the calling order: from the bottom to top
        $compositeValidator->addValidator(FielderValidator::create());
        $compositeValidator->addValidator(FielderRequiredValidator::create()); // runs first
    }
}
