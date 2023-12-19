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

    public function harvestFields($fields)
    {
        $harvest = $this->getCurrentHarvest($fields);

        $this->owner->extend('updateHarvest', $harvest);

        return $harvest;
    }

    public function harvestSettingsFields($fields)
    {
        $harvest = $this->getCurrentHarvest($fields);

        $this->owner->extend('updateHarvestSettings', $harvest);

        return $harvest;
    }

    public function harvestCompositeValidator($validator)
    {
        $harvest = $this->getCurrentHarvest();

        if ($harvest)
        {
            $this->owner->extend('updateHarvestCompositeValidator', $harvest);

            $validator->addValidator(RequiredFields::create($harvest->getRequireFields()));
        }
    }

    public function harvestValidate($result)
    {
        $harvest = $this->getCurrentHarvest();

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
            $this->harvestCompositeValidator($compositeValidator);
        }
    }

    public function updateCMSFields($fields)
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $this->harvestFields($fields);
        }
    }

    public function updateSettingsFields($fields)
    {
        if (method_exists($this->owner, 'harvestSettings'))
        {
            $this->harvestSettingsFields($fields);
        }
    }

    public function validate($result)
    {
        if (method_exists($this->owner, 'harvest'))
        {
            $this->harvestValidate($result);
        }
    }
}
