<?php

namespace Goldfinch\Harvest\Extensions;

use Goldfinch\Harvest\Harvest;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\Forms\CompositeValidator;

class HarvestExtension extends DataExtension
{
    public function harvestFields($fields)
    {
        $harvest = new Harvest($fields, $this);

        $this->owner->goldfinchHarvest = $harvest;

        return $this->owner->harvest($harvest);
    }

    public function harvestSettingsFields($fields)
    {
        $harvest = new Harvest($fields, $this);

        $this->owner->goldfinchHarvest = $harvest;

        return $this->owner->harvestSettings($harvest);
    }

    public function harvestCompositeValidator($validator)
    {
        if ($this->owner->goldfinchHarvest)
        {
            $validator->addValidator(RequiredFields::create($this->owner->goldfinchHarvest->getRequiredFields()));
        }
    }

    public function harvestValidate($result)
    {
        $error = $this->owner->goldfinchHarvest ? $this->owner->goldfinchHarvest->getError() : null;

        if ($error)
        {
            $result->addError($error);
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
