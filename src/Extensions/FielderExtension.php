<?php

namespace Goldfinch\Fielder\Extensions;

use Goldfinch\Fielder\Validator;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\ChangeSet;
use SilverStripe\Versioned\ChangeSetItem;

class FielderExtension extends DataExtension
{
    public function validate($result)
    {
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
            return Validator::create($this, $result)->validate();
        }
    }
}
