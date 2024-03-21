<?php

namespace Goldfinch\Fielder\Forms;

use Goldfinch\Fielder\Validator;
use SilverStripe\Forms\Validator as SSValidator;

class FielderValidator extends SSValidator
{
    public function php($data): bool
    {
        list($fielder, $fielderSettings) = $this->form->Fields()->getFielder();

        $valid = true;

        if ($fielder || $fielderSettings) {

            // ! removing these if-else conditions  (only lines with // !) leaving the rest as is will run both validation (main and settings) despite which one is currently vieweing
            if (isset($this->form->extraClasses['CMSPageSettingsController'])) { // !

                if ($fielderSettings) {
                    $fielder = $fielderSettings;

                    $results = Validator::create($data, $fielder)->validate();

                    if (!empty($results)) {

                        if (isset($results['errors']) && count($results['errors'])) {

                            foreach ($results['errors'] as $field => $errors) {
                                foreach ($errors as $error) {
                                    $this->result->addFieldError($field, $error, 'error', null, 'html');

                                    // .. all messages examples are below
                                }
                            }
                        }
                    }

                    // custom errors

                    if ($fielder->getError()) {
                        list($message, $messageType, $code, $cast) = $fielder->getError();
                        $this->result->addError($message, $messageType, $code, $cast);
                    }
                }

            } else if ($fielder) { // !

                $results = Validator::create($data, $fielder)->validate();

                if (!empty($results)) {

                    if (isset($results['errors']) && count($results['errors'])) {

                        foreach ($results['errors'] as $field => $errors) {
                            foreach ($errors as $error) {
                                $this->result->addFieldError($field, '<span style="display: block">'.$error.'</span>', 'error', null, 'html');
                            }
                        }
                    }
                }

                // custom errors

                if ($fielder->getError()) {
                    list($message, $messageType, $code, $cast) = $fielder->getError();
                    $this->result->addError($message, $messageType, $code, $cast);
                }
            } // !
        }

        return $valid;
    }

    public function canBeCached(): bool
    {
        return true;
    }
}
