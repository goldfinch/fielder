<?php

namespace Goldfinch\Fielder\Extensions;

use App\Forms\FielderValidator;
use Goldfinch\Fielder\Validator;
use SilverStripe\Forms\FieldList;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Versioned\ChangeSet;
use SilverStripe\Forms\RequiredFields;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\Versioned\ChangeSetItem;
use SilverStripe\Forms\CompositeValidator;

class FielderExtension extends DataExtension
{
    protected $fieldsWithFielder = [];

    public function updateCMSFields(FieldList $fields)
    {
        // echo '<!--FielderExtension-->'.PHP_EOL;
    }

    public function intFielder($fields)
    {
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 9); // 5
        $caller = $backtrace[4]['function'];
        $allowedFns = ['updateCMSFields', 'getCMSFields', 'updateSettingsFields', 'getSettingsFields'];

        if (in_array($caller, $allowedFns)) {

            // ! THIS needs for mergin multiple updateSettingsFields / updateCMSFields
            // $current = $this->getCurrentFielderFieldList($caller);
            // if ($current && $backtrace[8]['function'] != 'validate') {
            //     // make sure cycle within validate is skiped (causing all fielder fields missing at the end of submition, reloading page with missing fields)
            //     return $current;
            // }
            //     // update new FieldList with an existing Fielder
            //     $fields->items = $current->getFielder()->getFields()->items;
            //     $fields->setFielder($current->getFielder());
            //     // dd($current->getFielder()->getFields()->items());
            //     // $fields->items = [];
            //     // return $fields;
            //     // return $current;
            // } else {

            // }

            $fields->fielder($this);
            // $this->fieldsWithFielder[get_class($this->owner)] = $fields;
            $this->fieldsWithFielder[get_class($this->owner)][$caller] = $fields;

            return $fields;
        }

        throw new Exception('Fielder can only be called in: ' . implode(', ', $allowedFns));
    }

    public function getCurrentFielderFieldList($caller)
    {
        $class = get_class($this->owner);

        // if (isset($this->fieldsWithFielder[$class])) {
        //     return $this->fieldsWithFielder[$class];
        // }
        if (isset($this->fieldsWithFielder[$class][$caller])) {
            return $this->fieldsWithFielder[$class][$caller];
        }
    }

    public function updateCMSCompositeValidator(CompositeValidator $compositeValidator): void
    {
        // foreach ($compositeValidator->getValidators() as $v) {
            // dump($v->form);
        // }
        // $this->form->Fields()
        $compositeValidator->addValidator(FielderValidator::create());
        // $compositeValidator->addValidator(RequiredFields::create([
        //     'Content',
        // ]));
    }

    public function validate(ValidationResult $validationResult)
    {
        // $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 15);
        // // dump(collect($backtrace)->pluck('function'));
        // // dump($backtrace);
        // $current = $this->getCurrentFielderFieldList('updateSettingsFields');

        // if ($current && $this->owner->isChanged()) {

        //     $fielder = $current->getFielder();
        //     // dump($fielder->getValidatorRules());

        //     if ($fielder && $fielder->getError()) {
        //         $result->addError($fielder->getError());
        //     }

        //     $return = Validator::create($this, $result, $fielder)->validate();

        //     // if (!$fielder && method_exists($this->owner, 'getSettingsFields')) {
        //     //     $fielder = $this->owner->getSettingsFields()->getFielder();
        //     // }

        //     // if (
        //     //     $fielder &&
        //     //     $this->owner->isChanged() &&
        //     //     !in_array(get_class($this->owner), [
        //     //         ChangeSetItem::class,
        //     //         ChangeSet::class,
        //     //     ])
        //     // ) {
        //     //     Validator::create($this, $result)->validate();
        //     // }
        // }

        // if (!isset($return) || $return->isValid()) {

        //     $current = $this->getCurrentFielderFieldList('updateCMSFields');

        //     if ($current && $this->owner->isChanged()) {

        //         $fielder = $current->getFielder();
        //         // dump($fielder->getValidatorRules());

        //         if ($fielder && $fielder->getError()) {
        //             $result->addError($fielder->getError());
        //         }

        //         $return = Validator::create($this, $result, $fielder)->validate();
        //     }
        // }

        // if (isset($return)) {
        //     return $return;
        // }
    }
}
