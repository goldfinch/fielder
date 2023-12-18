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

        $this->goldfinchHarvest = $harvest;

        return $this->owner->harvest($harvest);
    }

    public function harvestSettingsFields($fields)
    {
        $harvest = new Harvest($fields, $this);

        $this->goldfinchHarvest = $harvest;

        return $this->owner->harvestSettings($harvest);
    }

    public function harvestCompositeValidator($validator)
    {
        $validator->addValidator(RequiredFields::create($this->goldfinchHarvest->getRequiredFields()));
    }

    public function harvestValidate($result)
    {
        $error = $this->goldfinchHarvest->getError();

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
