<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Validator;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\ChangeSet;
use SilverStripe\Versioned\ChangeSetItem;

class FielderExtension extends DataExtension
{
    public $fieldsWithFielder;

    public function intFielder($fields)
    {
        $fields->fielder($this->owner);

        $this->fieldsWithFielder = $fields;
    }

    public function validate($result)
    {
        if ($this->fieldsWithFielder) {

            $fielder = $this->owner->getCMSFields()->getFielder();

            if (!$fielder && method_exists($this->owner, 'getSettingsFields')) {
                $fielder = $this->owner->getSettingsFields()->getFielder();
            }

            if ($fielder && $fielder->getError()) {
                $result->addError($fielder->getError());
            }

            if (
                $fielder &&
                $this->owner->isChanged() &&
                !in_array(get_class($this->owner), [
                    ChangeSetItem::class,
                    ChangeSet::class,
                ])
            ) {
                Validator::create($this, $result)->validate();
            }
        }
    }
}
