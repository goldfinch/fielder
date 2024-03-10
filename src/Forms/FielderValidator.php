<?php

namespace App\Forms;

use Goldfinch\Fielder\Validator;
use SilverStripe\Forms\Validator as SSValidator;

/**
 * Validates the internal state of all fields in the form.
 */
class FielderValidator extends SSValidator
{
    public function php($data): bool
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 19); // 5
        // dump($this, $this->form);

        list($fielder, $fielderSettings) = $this->form->Fields()->getFielder();
        // dump($this->form->Fields()->getFielder()->getValidatorRules());
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
                }

            } else if ($fielder) { // !

                $results = Validator::create($data, $fielder)->validate();

                if (!empty($results)) {

                    if (isset($results['errors']) && count($results['errors'])) {

                        foreach ($results['errors'] as $field => $errors) {
                            foreach ($errors as $error) {
                                $this->result->addFieldError($field, $error, 'error', null, 'html');
                                // $this->result->addFieldError($field, $error, 'good', null, 'html');
                                // $this->result->addFieldError($field, $error, 'info', null, 'html');
                                // $this->result->addFieldError($field, $error, 'warning', null, 'html');

                                // $this->result->addError('custom error', 'warning');
                                // $this->result->addMessage('custom message', 'good');

                                // $this->validationError($field, $error, 'required');
                                // $this->validationError($field, $error, 'validation');
                                // $this->validationError($field, $error, 'message');
                                // $this->validationError($field, $error, 'bad');
                            }
                        }
                    }
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
