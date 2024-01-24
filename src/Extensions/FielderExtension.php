<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Fielder;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\CompositeValidator;

class FielderExtension extends DataExtension
{
    public function getCurrentFielder($fields = null)
    {
        if (method_exists($this->owner, 'fielder')) {
            $fielder = new Fielder(
                $fields ?? $this->owner->getCMSFields(),
                $this->owner,
            );
            $this->owner->fielder($fielder);
            $this->owner->extend('updateFielder', $fielder);

            return $fielder;
        }
    }

    public function getCurrentFielderSettings($fields = null)
    {
        if (method_exists($this->owner, 'fielderSettings')) {
            $fielder = new Fielder(
                $fields ?? $this->owner->getSettingsFields(),
                $this->owner,
            );
            $this->owner->fielderSettings($fielder);
            $this->owner->extend('updateFielderSettings', $fielder);

            return $fielder;
        }
    }

    public function fielderFields($fields)
    {
        $fielder = $this->owner->getCurrentFielder($fields);

        return $fielder;
    }

    public function fielderSettingsFields($fields)
    {
        $fielder = $this->owner->getCurrentFielderSettings($fields);

        return $fielder;
    }

    public function fielderCompositeValidator($validator)
    {
        $fielder = $this->owner->getCurrentFielder();

        if ($fielder) {
            $this->owner->extend('updateFielderCompositeValidator', $fielder);

            $validator->addValidator(
                RequiredFields::create($fielder->getRequireFields()),
            );
        }
    }

    public function fielderValidate($result)
    {
        $fielder = $this->owner->getCurrentFielder();

        $this->owner->extend('updateFielderValidate', $fielder);

        if ($fielder && $fielder->getError()) {
            $result->addError($fielder->getError());
        }
    }

    public function updateCMSCompositeValidator(
        CompositeValidator $compositeValidator,
    ): void {
        if (method_exists($this->owner, 'fielder')) {
            $this->owner->fielderCompositeValidator($compositeValidator);
        }
    }

    public function updateCMSFields($fields)
    {
        if (method_exists($this->owner, 'fielder')) {
            $this->owner->fielderFields($fields);
        }
    }

    public function updateSettingsFields($fields)
    {
        if (method_exists($this->owner, 'fielderSettings')) {
            $this->owner->fielderSettingsFields($fields);
        }
    }

    public function validate($result)
    {
        if (method_exists($this->owner, 'fielder')) {
            $this->owner->fielderValidate($result);
        }
    }
}
