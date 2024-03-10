<?php

namespace Goldfinch\Fielder\Forms;

use SilverStripe\ORM\ArrayLib;
use SilverStripe\Forms\RequiredFields;

class FielderRequiredValidator extends RequiredFields
{
    public function php($data)
    {
        list($fielder, $fielderSettings) = $this->form->Fields()->getFielder();

        $required = [];

        if ($fielder || $fielderSettings) {

            if (isset($this->form->extraClasses['CMSPageSettingsController'])) { // !
                $required = $fielder->getRequireFields();
            } else if ($fielder) { // !
                $required = $fielder->getRequireFields();
            }
        }

        $this->required = ArrayLib::valuekey($required);

        parent::php($data);
    }
}
