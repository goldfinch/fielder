<?php

namespace Goldfinch\Harvest\Extensions;

use Goldfinch\Harvest\Harvest;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\CompositeValidator;

class HarvestExtension extends DataExtension
{
    public function getCurrentHarvest($fields = null)
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $harvest = new Harvest($fields ?? $this->owner->getCMSFields(), $this->owner);
            $this->owner->harvest($harvest);

            return $harvest;
        }
    }

    public function getCurrentHarvestSettings($fields = null)
    {
        if (method_exists($this->owner, 'harvestSettings'))
        {
            $harvest = new Harvest($fields ?? $this->owner->getSettingsFields(), $this->owner);
            $this->owner->harvestSettings($harvest);

            return $harvest;
        }
    }

    public function harvestFields($fields)
    {
        $harvest = $this->owner->getCurrentHarvest($fields);

        $this->owner->extend('updateHarvest', $harvest);

        return $harvest;
    }

    public function harvestSettingsFields($fields)
    {
        $harvest = $this->owner->getCurrentHarvestSettings($fields);

        $this->owner->extend('updateHarvestSettings', $harvest);

        return $harvest;
    }

    public function harvestCompositeValidator($validator)
    {
        $harvest = $this->owner->getCurrentHarvest();

        if ($harvest)
        {
            $this->owner->extend('updateHarvestCompositeValidator', $harvest);

            $validator->addValidator(RequiredFields::create($harvest->getRequireFields()));
        }
    }

    public function harvestValidate($result)
    {
        $harvest = $this->owner->getCurrentHarvest();

        $this->owner->extend('updateHarvestValidate', $harvest);

        if ($harvest && $harvest->getError())
        {
            $result->addError($harvest->getError());
        }
    }

    public function updateCMSCompositeValidator(CompositeValidator $compositeValidator): void
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $this->owner->harvestCompositeValidator($compositeValidator);
        }
    }

    public function updateCMSFields($fields)
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $this->owner->harvestFields($fields);
        }
    }

    public function updateSettingsFields($fields)
    {
        if (method_exists($this->owner, 'harvestSettings'))
        {
            $this->owner->harvestSettingsFields($fields);
        }
    }

    public function validate($result)
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $this->owner->harvestValidate($result);
        }
    }
}
