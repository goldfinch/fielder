<?php

namespace Goldfinch\Harvest\Traits;

trait HarvestTrait {

    public function getCMSFields()
    {
        return $this->harvestFields(parent::getCMSFields());
    }

    public function getSettingsFields()
    {
        return $this->harvestSettingsFields(parent::getSettingsFields());
    }
}
