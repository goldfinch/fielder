<?php

namespace Goldfinch\Harvest\Traits;

trait HarvestTrait {

    // Fields could be extended by other external modules which sometimes leads to a mismatch bundle due to the sequence. To make sure harvest() and harvestSettings() received the latest $fields we use this trait

    public function getCMSFields()
    {
        return $this->harvestFields(parent::getCMSFields())->getFields();
    }

    public function getSettingsFields()
    {
        return $this->harvestSettingsFields(parent::getSettingsFields())->getFields();
    }
}
