<?php

namespace Goldfinch\Fielder\Traits;

use Goldfinch\Fielder\Validator;

trait FielderTrait
{
    // Fields could be extended by other external modules which sometimes leads to a mismatch bundle due to the sequence. To make sure fielder() and fielderSettings() received the latest $fields we use this trait

    public function getCMSFields()
    {
        return $this->fielderFields(parent::getCMSFields())->getFields();
    }

    public function getSettingsFields()
    {
        return $this->fielderSettingsFields(
            parent::getSettingsFields(),
        )->getFields();
    }

    public function validate()
    {
        return Validator::create($this, parent::validate())->validate();
    }
}
