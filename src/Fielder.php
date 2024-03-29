<?php

namespace Goldfinch\Fielder;

use Closure;
use Exception;
use Goldfinch\Fielder\Grid;
use Illuminate\Support\Arr;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\SS_List;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TabSet;
use SilverStripe\Core\Extension;
use SilverStripe\Security\Group;
use SilverStripe\Forms\DateField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\FileField;
use SilverStripe\Forms\FormField;
use SilverStripe\Forms\TextField;
use SilverStripe\Forms\TimeField;
use gorriecoe\LinkField\LinkField;
use LeKoala\Encrypt\EncryptHelper;
use SilverStripe\Forms\EmailField;
use SilverStripe\Forms\FieldGroup;
use SilverStripe\Forms\FormAction;
use SilverStripe\Forms\LabelField;
use SilverStripe\Forms\MoneyField;
use SilverStripe\Forms\HeaderField;
use SilverStripe\Forms\HiddenField;
use SilverStripe\Forms\LookupField;
use SilverStripe\TagField\TagField;
use SilverStripe\CMS\Model\SiteTree;
use SilverStripe\Core\Config\Config;
use SilverStripe\Forms\ListboxField;
use SilverStripe\Forms\LiteralField;
use SilverStripe\Forms\NumericField;
use SilverStripe\Forms\PopoverField;
use SGN\HasOneEdit\HasOneUploadField;
use SilverStripe\Forms\CheckboxField;
use SilverStripe\Forms\CurrencyField;
use SilverStripe\Forms\DatalessField;
use SilverStripe\Forms\DatetimeField;
use SilverStripe\Forms\DropdownField;
use SilverStripe\Forms\NullableField;
use SilverStripe\Forms\PasswordField;
use SilverStripe\Forms\ReadonlyField;
use SilverStripe\Forms\TextareaField;
use SilverStripe\ORM\FieldType\DBInt;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\OptionsetField;
use SilverStripe\Forms\SelectionGroup;
use SilverStripe\ORM\FieldType\DBEnum;
use SilverStripe\ORM\FieldType\DBYear;
use SilverStripe\ORM\ValidationResult;
use SilverStripe\ORM\FieldType\DBFloat;
use Goldfinch\IconField\Forms\IconField;
use SilverStripe\AnyField\Form\AnyField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\ORM\FieldType\DBBigInt;
use SilverStripe\ORM\FieldType\DBDouble;
use SilverStripe\ORM\FieldType\DBLocale;
use SilverStripe\Forms\HTMLReadonlyField;
use SilverStripe\Forms\SingleLookupField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\FieldType\DBBoolean;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\TagField\StringTagField;
use Goldfinch\GoogleFields\Forms\MapField;
use Goldfinch\VideoField\Forms\VideoField;
use DNADesign\Elemental\Models\BaseElement;
use JonoM\FocusPoint\Forms\FocusPointField;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\TagField\ReadonlyTagField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use Goldfinch\GoogleFields\Forms\PlaceField;
use SilverStripe\AnyField\Form\ManyAnyField;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\ORM\FieldType\DBPercentage;
use SilverShop\HasOneField\HasOneButtonField;
use gorriecoe\LinkField\Forms\HasOneLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\ConfirmedPasswordField;
use TractorCow\AutoComplete\AutoCompleteField;
use Goldfinch\JSONEditor\Forms\JSONEditorField;
use SilverStripe\CMS\Forms\AnchorSelectorField;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\VersionedAdmin\Forms\DiffField;
use Goldfinch\ImageEditor\Forms\ImageCoordsField;
use Heyday\ColorPalette\Fields\ColorPaletteField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use Goldfinch\Shortcode\ORM\FieldType\DBSCVarchar;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use Goldfinch\ImageEditor\Forms\EditableUploadField;
use SilverStripe\AssetAdmin\Forms\PreviewImageField;
use Innoweb\InternationalPhoneNumberField\ORM\DBPhone;
use KevinGroeger\CodeEditorField\Forms\CodeEditorField;
use Kinglozzer\MultiSelectField\Forms\MultiSelectField;
use RyanPotter\SilverStripeColorField\Forms\ColorField;
use Heyday\ColorPalette\Fields\GroupedColorPaletteField;
use LittleGiant\SilverStripeImagePoints\Forms\PointField;
use NSWDPC\Forms\ImageSelectionField\ImageSelectionField;
use SilverStripe\LinkField\Form\LinkField as LinkSSField;
use Goldfinch\ImageEditor\Forms\EditableSortableUploadField;
use Dynamic\CountryDropdownField\Fields\CountryDropdownField;

class Fielder
{
    /**
        TODO fields:

        - DBClassName
        - Foreignkey
        - HTMLFragment
        - HTMLVarchar
        - MultiEnum
        - PolymorphicForeignKey
        - PrimaryKey
        - Text
        - Varchar
     */

    private $fields = null;

    private $initialFields = null;

    private $allFieldsRemoved = false;

    private $validatorRules = [];

    private $error = null;

    private $parent = null;

    private $extension = null;

    public function getParentObject()
    {
        return $this->parent;
    }

    public function __construct($fields, $parent)
    {
        if (is_subclass_of($parent, Extension::class)) {
            $this->extension = $parent;
            $this->parent = $parent->getOwner();
        } else {
            $this->parent = $parent;
        }

        $this->fields = $fields;
        $this->initialFields = clone $this->fields;
    }

    public function clearTab($tabname)
    {
        $this->removeFieldsInTab($tabname);
    }

    public function removeFieldsInTab($tabname)
    {
        $tab = $this->findTab($tabname);

        if ($tab) {
            foreach ($tab->getChildren() as $field) {

                if ($this->parent->dbObject($field->getName())) {
                    $this->remove($field->getName());
                } else {

                    if ((get_class($field) == CompositeField::class || is_subclass_of($field, CompositeField::class)) && !$this->dataField($field->getName())) {

                        $this->remove($field->getName());
                    } else {
                        if (get_class($field) == UploadField::class) {
                            $this->remove($field->getName());
                        }
                    }
                }
            }
        }
    }

    // public function removeAllInTab($tab)
    // {
    //     $fltFields = $this->fields
    //         ->findTab($tab)
    //         ->getChildren()
    //         ->flattenFields();

    //     // Escpe some sensitive fields for BaseElement
    //     if (
    //         is_subclass_of($this->getParent()->getOwner(), BaseElement::class)
    //     ) {
    //         $array = array_flip(array_keys($fltFields->map()->toArray()));
    //         unset($array['Version']);
    //         unset($array['AvailableGlobally']);
    //         unset($array['VirtualLookupTitle']);
    //         unset($array['TopPageID']);
    //         unset($array['AbsoluteLink']);
    //         unset($array['LiveLink']);
    //         unset($array['StageLink']);
    //         $array = array_flip($array);

    //         $this->remove($array);
    //     }
    // }

    public function disable($fieldNameOrFields, $state = true)
    {
        if (is_string($fieldNameOrFields)) {
            $field = $this->dataField($fieldNameOrFields);

            if ($field) {
                $field->setDisabled($state);
            }
        } else if (is_array($fieldNameOrFields)) {
            foreach ($fieldNameOrFields as $fieldname) {
                $field = $this->dataField($fieldname);

                if ($field) {
                    $field->setDisabled($state);
                }
            }
        }
    }

    public function readonly($fieldNameOrFields, $state = true)
    {
        if (is_string($fieldNameOrFields)) {
            $field = $this->dataField($fieldNameOrFields);

            if ($field) {
                $field->setReadonly($state);
            }
        } else if (is_array($fieldNameOrFields)) {
            foreach ($fieldNameOrFields as $fieldname) {
                $field = $this->dataField($fieldname);

                if ($field) {
                    $field->setReadonly($state);
                }
            }
        }
    }

    public function description($fieldNameOrFields, $description = null)
    {
        if (is_string($fieldNameOrFields) && $description) {
            $field = $this->dataField($fieldNameOrFields);

            if ($field) {
                $field->setDescription($description);
            }
        } else if (is_array($fieldNameOrFields)) {
            foreach ($fieldNameOrFields as $fieldname => $description) {
                $field = $this->dataField($fieldname);

                if ($field) {
                    $field->setDescription($description);
                }
            }
        }
    }

    public function findTab($tabname)
    {
        return $this->fields->findTab($tabname);
    }

    public function field($name, $title = null)
    {
        $obj = $this->parent->dbObject($name);

        return $obj ? $obj->scaffoldFormField($title) : null;
    }

    public function remove($fields)
    {
        $this->fields->removeByName($fields);
    }

    public function fields($fieldsList)
    {
        foreach ($fieldsList as $tab => $list) {
            if (is_array($list)) {
                foreach ($list as $li) {
                    $this->fields->addFieldToTab($tab, $li);
                }
            } else {
                $this->fields->addFieldToTab($list);
            }
        }

        return $this->fields;
    }

    public function insertAfter($target, $field)
    {
        if (is_array($field)) {
            foreach (array_reverse($field) as $f) {
                $this->fields->insertAfter($target, $f);
            }
        } else {
            $this->fields->insertAfter($target, $field);
        }
    }

    public function insertBefore($target, $field)
    {
        if (is_array($field)) {
            foreach ($field as $f) {
                $this->fields->insertBefore($target, $f);
            }
        } else {
            $this->fields->insertBefore($target, $field);
        }
    }

    public function reorder($fields)
    {
        $this->fields->changeFieldOrder($fields);
    }

    public function reorderTabs($tabs)
    {
        foreach ($tabs as $tab) {
            $tabEx = explode('.', $tab);
            $tabInst = $this->fields->fieldByName($tab);
            $this->fields->removeFieldFromTab($tabEx[0], $tabEx[1]);
            $this->fields->fieldByName($tabEx[0])->push($tabInst);
        }
    }

    public function toTab($tab, $fields)
    {
        $this->fields([$tab => $fields]);
    }

    /*
        Covered:

        + isEqualTo
        + isNotEqualTo
        + isGreaterThan
        + isLessThan
        - contains
        - startsWith
        - endsWith
        + isEmpty
        + isNotEmpty
        - isBetween
        + isChecked
        + isNotChecked()
        - hasCheckedOption
        - hasCheckedAtLeast
        - hasCheckedLessThan

        ||
        &&
    */
    private function displayLogic($fn, $condition, $fields)
    {
        if (is_array($condition)) {

            if (count($condition) == 3) {
                list($target, $operator, $target2) = $condition;

                $empty = false;

                if ($target2 === null) {
                    $empty = true;
                }

                if ($operator == '==') {
                    if ($empty) {
                        return $this->wrapper(...$fields)->$fn($target)->isEmpty()->end();
                    } else {

                        $c = $this->wrapper(...$fields)->$fn($target);

                        if (is_array($target2)) {
                            // multi
                            $started = 0;

                            foreach ($target2 as $e) {

                                if ($started) {
                                    $c = $c->isEqualTo($e);
                                } else {
                                    $c = $c->orIf($target)->isEqualTo($e);

                                    $started = 1;
                                }
                            }

                            return $c->end();

                        } else {
                            return $c->isEqualTo($target2)->end();
                        }
                    }
                } else if ($operator == '!=') {

                    if ($empty) {
                        return $this->wrapper(...$fields)->$fn($target)->isNotEmpty()->end();
                    } else {

                        $c = $this->wrapper(...$fields)->$fn($target);

                        if (is_array($target2)) {
                            // multi
                            $started = 0;

                            foreach ($target2 as $e) {

                                if ($started) {
                                    $c = $c->isNotEqualTo($e);
                                } else {
                                    $c = $c->andIf($target)->isNotEqualTo($e);

                                    $started = 1;
                                }
                            }

                            return $c->end();

                        } else {
                            return $c->isNotEqualTo($target2)->end();
                        }
                    }
                } else if ($operator == '>') {
                    return $this->wrapper(...$fields)->$fn($target)->isGreaterThan($target2)->end();
                } else if ($operator == '<') {
                    return $this->wrapper(...$fields)->$fn($target)->isLessThan($target2)->end();
                }
            } else {
                throw new Exception($fn . ' should contain three parameters');
            }
        } else {

            $excl = false;

            if ($condition[0] == '!') {
                $excl = true;
                $condition = substr($condition, 1);
            }

            if (strpos($condition, '||') !== false) {
                // TODO
            }

            if (strpos($condition, '&&') !== false) {
                // TODO
            }

            $db = $this->parent->dbObject($condition);

            if (get_class($db) == DBBoolean::class) {
                if ($excl) {
                    return $this->wrapper(...$fields)->$fn($condition)->isNotChecked()->end();
                } else {
                    return $this->wrapper(...$fields)->$fn($condition)->isChecked()->end();
                }
            } else {
                //
            }
        }
    }

    public function displayIf($condition, $fields)
    {
        return $this->displayLogic(__FUNCTION__, $condition, $fields);
    }

    public function displayUnless($condition, $fields)
    {
        return $this->displayLogic(__FUNCTION__, $condition, $fields);
    }

    public function hideIf($condition, $fields)
    {
        return $this->displayLogic(__FUNCTION__, $condition, $fields);
    }

    public function hideUnless($condition, $fields)
    {
        return $this->displayLogic(__FUNCTION__, $condition, $fields);
    }

    public function freshFields($fieldsList)
    {
        $this->removeAllCurrent();

        return $this->fields($fieldsList);
    }

    public function dataField($name)
    {
        if ($this->allFieldsRemoved) {
            return $this->initialFields->dataFieldByName($name);
        } else {
            return $this->fields->dataFieldByName($name);
        }
    }

    public function removeAll()
    {
        foreach ($this->fields->flattenFields() as $field) {
            if (!in_array(get_class($field), [Tab::class, TabSet::class])) {
                $this->fields->removeByName($field->getName());
            }
        }

        $this->allFieldsRemoved = true;
    }

    public function removeAllCurrent()
    {
        $db = Config::inst()->get(
            get_class($this->parent),
            'db',
            CONFIG::UNINHERITED,
        );
        $has_one = Config::inst()->get(
            get_class($this->parent),
            'has_one',
            CONFIG::UNINHERITED,
        );
        $belongs_to = Config::inst()->get(
            get_class($this->parent),
            'belongs_to',
            CONFIG::UNINHERITED,
        );
        $has_many = Config::inst()->get(
            get_class($this->parent),
            'has_many',
            CONFIG::UNINHERITED,
        );
        $many_many = Config::inst()->get(
            get_class($this->parent),
            'many_many',
            CONFIG::UNINHERITED,
        );
        $belongs_many_many = Config::inst()->get(
            get_class($this->parent),
            'belongs_many_many',
            CONFIG::UNINHERITED,
        );

        if ($db) {
            $this->remove(array_keys($db));
        }

        if ($has_one) {
            $this->remove(array_keys($has_one));
            $has_oneID = Arr::mapWithKeys($has_one, function ($item, $key) {
                return [$key . 'ID' => $item];
            });

            $this->remove(array_keys($has_oneID));
        }

        if ($belongs_to) {
            $this->remove(array_keys($belongs_to));
            $belongs_toID = Arr::mapWithKeys($belongs_to, function (
                $item,
                $key,
            ) {
                return [$key . 'ID' => $item];
            });
            $this->remove(array_keys($belongs_toID));
        }

        if ($has_many) {
            $this->remove(array_keys($has_many));
        }

        if ($many_many) {
            $this->remove(array_keys($many_many));
        }

        if ($belongs_many_many) {
            $this->remove(array_keys($belongs_many_many));
        }

        // by some reason, 'FocusPoint' db type not being removed through remove()
        // only works when `FocusPoint` field name renamed to sentence case `Focuspoint`
        foreach ($db as $k => $f) {
            if (strtolower($f) == 'focuspoint') {
                $this->remove(ucfirst(strtolower($k)));
            }
        }
    }

    public function validate($rules): void
    {
        $this->setValidatorRules($rules);
    }

    public function setValidatorRules($rules)
    {
        foreach ($rules as $field => $rule)
        {
            if (!isset($this->validatorRules[$field])) {
                $this->validatorRules[$field] = [];
            }

            if (is_array($rule)) {
                $this->validatorRules[$field] = array_merge($this->validatorRules[$field], $rule);
            } else if (is_object($rule) && get_class($rule) == Closure::class) {
                if (!isset($this->validatorRules[$field])) {
                    $this->validatorRules[$field] = [];
                }

                $this->validatorRules[$field] = array_merge($this->validatorRules[$field], [$rule]);
            } else {
                $exRule = explode('|', $rule);

                foreach ($exRule as $exr) {
                    $this->validatorRules[$field] = array_merge($this->validatorRules[$field], [$exr]);
                }
            }
        }
    }

    public function getValidatorRules()
    {
        return $this->validatorRules;
    }

    public function required($fields)
    {
        $this->setRequireFields($fields);
    }

    public function setRequireFields($fields)
    {
        $requiredFields = is_array($fields) ? $fields : [$fields];
        $ra = [];

        foreach ($requiredFields as $rf) {

            $ra[$rf] = 'required';
        }

        $this->setValidatorRules($ra);
    }

    public function getRequireFields()
    {
        $rules = [];

        foreach ($this->getValidatorRules() as $field => $r) {
            if (in_array('required', $r)) {
                $rules[] = $field;
            }
        }

        return $rules;
    }

    public function addError($message, $messageType = ValidationResult::TYPE_ERROR, $code = null, $cast = ValidationResult::CAST_HTML)
    {
        $this->error = [$message, $messageType, $code, $cast];
    }

    public function getError()
    {
        return $this->error;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function getParent()
    {
        return $this->parent;
    }

    public function makeReadonly()
    {
        $this->fields->makeReadonly();

        return $this;
    }

    private function lookForSource(&$name, &$title, &$source)
    {
        if (empty($source)) {
            $relation = $this->parent->getRelationType($name);

            if (
                $relation &&
                ($relation == 'has_one' || $relation == 'belongs_to')
            ) {
                $object = $this->parent->$name();
                $class = get_class($object);
                $source = $class::get()->map();
                if (!$title) {
                    $title = $name;
                }
                $name .= 'ID';
            } elseif (
                $relation == 'many_many' ||
                $relation == 'has_many' ||
                $relation == 'belongs_many_many'
            ) {
                $object = $this->parent->$name();
                $class = $object->dataClass;
                $source = $class::get()->map();
            }
        }
    }

    private function lookForSourceObject(&$name, &$title, &$sourceObject)
    {
        if (!$sourceObject) {
            $relation = $this->parent->getRelationType($name);

            if (
                $relation &&
                ($relation == 'has_one' || $relation == 'belongs_to')
            ) {
                $object = $this->parent->$name();
                $sourceObject = get_class($object);
                if (!$title) {
                    $title = $name;
                }
                $name .= 'ID';
            } elseif (
                $relation == 'many_many' ||
                $relation == 'has_many' ||
                $relation == 'belongs_many_many'
            ) {
                $object = $this->parent->$name();
                $sourceObject = $object->dataClass;
            }
        }
    }

    /**
     * ! Internal general fields
     */

    /* ------- [Basic] ------- */

    /**
     * DB Type: Boolean
     * Available methods:
     */
    public function checkbox($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return CheckboxField::create($name, $title, $value);
    }

    public function lineCheckbox(...$args)
    {
        // need here to remove an empty composite div in updated form template after publishing record
        $this->remove($args[0] . 'Group');

        return $this->composite($this->checkbox(...$args));
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function dropdown($name, $title = null, $source = [], $value = null)
    {
        $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return DropdownField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function readonlyField($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return ReadonlyField::create($name, $title, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function text($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return TextareaField::create($name, $title, $value);
    }

    /**
     * DB Type: SCVarchar
     */
    public function shortcode($name, $title = null)
    {
        return $this->field($name, $title);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function string(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return TextField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function password($name, $title = null, $value = '')
    {
        $this->existenceCheck($name);

        return PasswordField::create($name, $title, $value);
    }

    /* ------- [Actions] ------- */

    /**
     * --
     */
    public function action($action, $title = '', $form = null)
    {
        return FormAction::create($action, $title, $form);
    }

    /* ------- [Formatted input] ------- */

    /**
     * DB Type: *
     * Available methods:
     */
    public function passwordConfirmed(
        $name,
        $title = null,
        $value = '',
        $form = null,
        $showOnClick = false,
        $titleConfirmField = null,
    ) {
        $this->existenceCheck($name);

        return ConfirmedPasswordField::create(
            $name,
            $title,
            $value,
            $form,
            $showOnClick,
            $titleConfirmField,
        );
    }

    /**
     * DB Type: Currency
     * Available methods:
     */
    public function currency(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return CurrencyField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: Date
     * Available methods:
     */
    public function date(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return DateField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: Datetime
     * Available methods:
     */
    public function datetime(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return DatetimeField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function email(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return EmailField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->groupedDropdown('Name', 'Title', [
            'numbers' => [1 => 1, 2 => 2],
            'letters' => [1 => 'A', 2 => 'B'],
        ]),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function groupedDropdown(
        $name,
        $title = null,
        $source = [],
        $value = null,
    ) {
        $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return GroupedDropdownField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: HTMLText
     * Available methods:
     */
    public function html($name, $title = null, $value = '', $config = null)
    {
        $this->existenceCheck($name);

        return HTMLEditorField::create($name, $title, $value, $config);
    }

    /**
     * DB Type: Money
     * Available methods:
     */
    public function money($name, $title = null, $value = '')
    {
        $this->existenceCheck($name);

        return MoneyField::create($name, $title, $value);
    }

    /**
     * DB Type: Decimal | Float | Int
     * Available methods:
     */
    public function numeric(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return NumericField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->radio('Name', 'Title', [1 => 'Option 1', 2 => 'Option 2']),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function radio($name, $title = null, $source = [], $value = null)
    {
        $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return OptionsetField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->selectionGroup('Name', [
            $fielder->selectionGroupItem(
                'one',
                $fielder->literal('one', 'one view'),
                'one title'
            ),
            $fielder->selectionGroupItem(
                'two',
                $fielder->literal('two', 'two view'),
                'two title'
            ),
        ]),
     */
    public function selectionGroup($name, $items, $value = null)
    {
        $this->existenceCheck($name);

        return SelectionGroup::create($name, $items, $value);
    }

    public function selectionGroupItem($value, $fields = null, $title = null)
    {
        return SelectionGroup_Item::create($value, $fields, $title);
    }

    /**
     * DB Type: Time
     * Available methods:
     */
    public function time(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return TimeField::create($name, $title, $value, $maxLength, $form);
    }

    /* ------- [Structure] ------- */

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $fielder->composite([
            $fielder->string('Title'),
            $fielder->html('Text'),
        ]),
     */
    public function composite($children = null)
    {
        return CompositeField::create($children);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $fielder->group(
            $fielder->string('Title'),
            $fielder->html('Text'),
        )->setTitle('Group Title),
     */
    public function group($titleOrField = null, ...$otherFields)
    {
        return FieldGroup::create($titleOrField, $otherFields);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * FYI: $fields is FieldList already. Using this field we store new FieldList in FieldList
     *
     * Code example:
        ...$fielder->list([
            $fielder->string('Title'),
            $fielder->html('Text'),
        ]),
     */
    public function list($items = [])
    {
        return FieldList::create($items);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $fielder->tab('Primary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
     */
    public function tab($name, $titleOrField = null, $fields = null)
    {
        $this->existenceCheck($name);

        return Tab::create($name, $titleOrField, $fields);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $fielder->tabSet('MyTabSetName',
            $fielder->tab('Primary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
            $fielder->tab('Secondary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Banshee</b>')),
        ),
     */
    public function tabSet($name, $titleOrTab = null, $tabs = null)
    {
        $this->existenceCheck($name);

        return TabSet::create($name, $titleOrTab, $tabs);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $fielder->toggleComposite('MyToggle', 'Toggle', [
            $fielder->string('Title'),
            $fielder->text('Text')
        ]),
     */
    public function toggleComposite($name, $title, $children)
    {
        $this->existenceCheck($name);

        return ToggleCompositeField::create($name, $title, $children);
    }

    /* ------- [Files] ------- */

    /**
     * DB Type: -
     * Allowed relations: has_one | has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function upload($name, $title = null, SS_List $items = null)
    {
        $this->existenceCheck($name);

        return UploadField::create($name, $title, $items);
    }

    /**
     * DB Type: -
     * Allowed relations: has_one | has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function file($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return FileField::create($name, $title, $value);
    }

    /* ------- [Relations] ------- */

    /**
     * DB Type: *
     * Suits relations: has_many | many_many | belongs_many_many
     * Available methods:
     *
     * Code example:
        $fielder->checkboxSet('List', 'List', [1 => 'Set 1', 2 => 'Set 2']),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function checkboxSet(
        $name,
        $title = null,
        $source = [],
        $value = null,
    ) {
        $relation = $this->parent->getRelationType($name);

        if (in_array($relation, ['has_one', 'belongs_to'])) {
            return $this->returnError(
                $name,
                $name .
                    ': do not use <b>checkboxSet</b> on <b>' .
                    $relation .
                    '</b>',
            );
        }

        $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return CheckboxSetField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Allowed relations: has_one (!SiteTree)
     * Available methods:
     *
     * Code example:
        $fielder->dropdownTree('Page'),
     */
    public function dropdownTree(
        $name,
        $title = null,
        $sourceObject = null,
        $keyField = 'ID',
        $labelField = 'TreeTitle',
        $showSearch = true,
    ) {
        $this->lookForSourceObject($name, $title, $sourceObject);

        if (!is_subclass_of(new $sourceObject(), SiteTree::class) && $sourceObject != SiteTree::class) {
            return $this->returnError(
                $name,
                $name .
                    ': use <b>dropdownTree</b> only for a relationship that inherited <b>SiteTree</b> class',
            );
        }

        $this->existenceCheck($name);

        return TreeDropdownField::create(
            $name,
            $title,
            $sourceObject,
            $keyField,
            $labelField,
            $showSearch,
        );
    }

    /**
     * (!) only for Groups
     *
     * DB Type: *
     * Allowed relations: has_many | many_many
     * Available methods:
     */
    public function treeMultiSelect(
        $name,
        $title = null,
        $sourceObject = Group::class,
        $keyField = 'ID',
        $labelField = 'Title',
    ) {
        $this->existenceCheck($name);

        return TreeMultiselectField::create(
            $name,
            $title,
            $sourceObject,
            $keyField,
            $labelField,
        );
    }

    /**
     * DB Type: *
     * Allowed relations: has_many | many_many
     * Available methods:
     *
     * Code example:
        $fielder->grid('Services', 'Services')->build(),

        $fielder->grid('Services', 'Services', $this->Services())->build(),

        $fielder->grid('Cards', 'Cards')
            ->config('default')
            ->components([
                'add',
                'edit',
            ])
            ->remove([
                'add',
                'edit',
                'copy',
                'delete',
            ])->build(),
     */
    public function grid(
        $name,
        $title = null,
        SS_List $dataList = null,
        GridFieldConfig $config = null,
    ) {
        $this->existenceCheck($name);

        $grid = new Grid($this->fields, $this->parent);
        $grid->init($name, $title, $dataList, $config);

        return $grid;
    }

    /**
     * DB Type: *
     * Suits relations: has_many | many_many | belongs_many_many
     * Available methods:
     *
     * Code example:
        $fielder->listbox('List'),
     */
    public function listbox(
        $name,
        $title = null,
        $source = [],
        $value = null,
        $size = null,
    ) {
        $relation = $this->parent->getRelationType($name);

        if (in_array($relation, ['has_one', 'belongs_to'])) {
            return $this->returnError(
                $name,
                $name .
                    ': do not use <b>listbox</b> on <b>' .
                    $relation .
                    '</b>',
            );
        }

        $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return ListboxField::create($name, $title, $source, $value, $size);
    }

    /* ------- [Utility] ------- */

    // public function dataless(\DOMElement $node)
    // {
    //     return DatalessField::create($node);
    // }

    /**
     * DB Type: -
     * Available methods:
     */
    public function header($name, $title = null, $headingLevel = 2)
    {
        $this->existenceCheck($name);

        return HeaderField::create($name, $title, $headingLevel);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function hidden($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return HiddenField::create($name, $title, $value);
    }

    /**
     * DB Type: -
     * Available methods:
     */
    public function label($name, $title = null)
    {
        $this->existenceCheck($name);

        return LabelField::create($name, $title);
    }

    /**
     * DB Type: -
     * Available methods:
     */
    public function literal($name, $content)
    {
        $this->existenceCheck($name);

        return LiteralField::create($name, $content);
    }

    /**
     * ! Other general fields
     */

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->nullable($fielder->string('Text')),
     */
    public function nullable(FormField $valueField, $isNullLabel = null)
    {
        return NullableField::create($valueField, $isNullLabel);
    }

    // public function lookup($name, $title = null, $source = [], $value = null)
    // {
    //     return LookupField::create($name, $title, $source, $value);
    // }

    // public function popover($titleOrField = null, $otherFields = null)
    // {
    //     return PopoverField::create($titleOrField, $otherFields);
    // }

    // public function singleLookup($name, $title = null, $source = [], $value = null)
    // {
    //     return SingleLookupField::create($name, $title, $source, $value);
    // }

    /**
     * ! DB fields
     */

    /**
     * DB Type: Decimal
     * Available methods:
     */
    public function decimal(
        $name,
        $title = null,
        $wholeSize = 9,
        $decimalSize = 2,
        $defaultValue = 0,
    ) {
        if (!$this->isDBType($name, DBDecimal::class)) {
            return $this->returnTypeError($name, 'decimal');
        }

        // public function decimal($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(2);

        $this->existenceCheck($name);

        $field = new DBDecimal($name, $wholeSize, $decimalSize, $defaultValue);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Double
     * Available methods:
     */
    public function double($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBDouble::class)) {
            return $this->returnTypeError($name, 'double');
        }

        // public function double($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form);

        $this->existenceCheck($name);

        $field = new DBDouble($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Float
     * Available methods:
     */
    public function float($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBFloat::class)) {
            return $this->returnTypeError($name, 'float');
        }

        // public function float($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(null);

        $this->existenceCheck($name);

        $field = new DBFloat($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Year
     * Available methods:
     */
    public function year($name, $title = null, $options = [])
    {
        if (!$this->isDBType($name, DBYear::class)) {
            return $this->returnTypeError($name, 'year');
        }

        $this->existenceCheck($name);

        $field = new DBYear($name, ($options = []));
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Percentage | Percentage(6)
     * Available methods:
     */
    public function percentage($name, $title = null, $precision = 4)
    {
        if (!$this->isDBType($name, DBPercentage::class)) {
            return $this->returnTypeError($name, 'percentage');
        }

        $this->existenceCheck($name);

        $field = new DBPercentage($name, $precision);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Int
     * Available methods:
     */
    public function int($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBInt::class)) {
            return $this->returnTypeError($name, 'int');
        }

        $this->existenceCheck($name);

        $field = new DBInt($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: BigInt
     * Available methods:
     */
    public function bigInt($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBBigInt::class)) {
            return $this->returnTypeError($name, 'int');
        }

        $this->existenceCheck($name);

        $field = new DBBigInt($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Locale
     * Available methods:
     */
    public function locale($name, $title = null, $size = 16)
    {
        if (!$this->isDBType($name, DBLocale::class)) {
            return $this->returnTypeError($name, 'int');
        }

        $this->existenceCheck($name);

        $field = new DBLocale($name, $size);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Enum("Apple,Orange,Kiwi", "Kiwi")
     * Available methods:
     */
    public function enum($name, $title = null)
    {
        if (!$this->isDBType($name, DBEnum::class)) {
            return $this->returnTypeError($name, 'enum');
        }

        // public function enum($name, $title = null, $source = [], $value = null)
        // public function enum($name, $title = null, $enum = null, $default = 0, $options = [])
        // $field = new DBEnum($name, $enum, $default, $options);
        // return $field->scaffoldFormField($title);
        // return DropdownField::create($name, $title, $source, $value);

        $this->existenceCheck($name);

        $field = $this->field($name);

        if ($title) {
            $field->setTitle($title);
        }

         return $field;
    }

    // public function htmlText($name)
    // {
    //     return $this->field($name);
    // }

    // public function htmlFragment($name)
    // {
    //     return $this->field($name);
    // }

    // public function htmlVarchar($name)
    // {
    //     return $this->field($name);
    // }

    /**
     * ! External fields
     */

    /**
     * DB Type: -
     * Allowed relations: has_one | belongs_to
     * Available methods:
     *
        $fielder->objectLink('Project'),
     */
    public function objectLink(
        $relationName,
        $fieldName = null,
        $title = null,
        GridFieldConfig $customConfig = null,
        $useAutocompleter = true,
    ) {
        $this->existenceCheck($relationName . 'ID');

        // TODO: causing errors without this check, return null field instead (happens after dev/build only once)
        if (!$this->parent->ID) {
            return $this->literal('null', '');
        }

        return HasOneButtonField::create(
            $this->parent,
            $relationName,
            $fieldName,
            $title,
            $customConfig,
            $useAutocompleter,
        );
    }

    /**
     * DB Type: -
     * Allowed relations: has_one | belongs_to
     * Available methods:
     */
    public function object(
        $relationName,
        $title = null,
        $linkConfig = [],
        $useAutocompleter = false,
    ) {
        return HasOneLinkField::create(
            $this->parent,
            $relationName,
            $title,
            $linkConfig,
            $useAutocompleter,
        );
    }

    /**
     * DB Type: -
     * Allowed relations: many_many | belongs_many_many
     * Available methods:
     *
     * Code example:
        $fielder->multiSelect('Services'),
        $fielder->multiSelect('Services', 'Services', 'SortExtra'),
     */
    public function multiSelect(
        $name,
        $title = null,
        $sort = false,
        $source = null,
        $titleField = 'Title',
    ) {
        $relation = $this->parent->getRelationType($name);

        if (!in_array($relation, ['many_many', 'belongs_many_many'])) {
            return $this->returnError(
                $name,
                $name .
                    ': <b>multiSelect</b> is only for <b>many-many</b> relationship',
            );
        }

        $this->existenceCheck($name);

        return MultiSelectField::create(
            $name,
            $title,
            $this->parent,
            $sort,
            $source,
            $titleField,
        );
    }

    /**
     * DB Type: -
     * Allowed relations: has_one | has_many | many_many | belongs_many_many
     * Available methods:
     *
     * Code example:
        ...$fielder->media('Image'),
     */
    public function media($name, $title = null)
    {
        $this->existenceCheck($name);

        return EditableUploadField::create(
            $name,
            $title,
            $this->fields,
            $this->parent,
        )->getFields();
    }

    /**
     * DB Type: -
     * Allowed relations: has_many | many_many
     * Available methods:
     *
     * Code example:
        ...$fielder->mediaSortable('Images'),
     */
    public function mediaSortable($name, $title = null)
    {
        $this->existenceCheck($name);

        return EditableSortableUploadField::create(
            $name,
            $title,
            $this->fields,
            $this->parent,
        )->getFields();
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->color('Color', 'Color', ['yellow' => '#fee12f', 'pink' => '#eb83ad', 'green' => '#70cd77']),
     */
    public function color($name, $title = null, $source = [], $value = null)
    {
        $this->existenceCheck($name);

        return ColorPaletteField::create($name, $title, $source);
    }

    /**
     * DB Type: Varchar(7)
     * Available methods:

     * 1) yml
      RyanPotter\SilverStripeColorField\Forms\ColorField:
        colors:
          - '#1976D2'
          - '#2196F3'
          - '#BBDEFB'
          - '#FFFFFF'
          - '#FF4081'
          - '#212121'
          - '#727272'
          - '#B6B6B6'
     */
    public function colorPicker($name, $title = null, $value = '', $form = null)
    {
        $this->existenceCheck($name);

        return ColorField::create($name, $title, $value, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->colorGroup('Color', 'Color', ['Primary' => ['yellow' => '#fee12f', 'pink' => '#eb83ad'], 'Secondary' => ['green' => '#70cd77']]),
     */
    public function colorGroup(
        $name,
        $title = null,
        $source = [],
        $value = null,
    ) {
        $this->existenceCheck($name);

        return GroupedColorPaletteField::create($name, $title, $source);
    }

    /**
     * DB Type: Goldfinch\JSONEditor\ORM\FieldType\DBJSONText
     * Available methods:
     *
     * Code example:
     * 1) create schema fiel: app/_schema/Page-Parameters.json (app/_schema/{parent-classname}-{field-mame}.json)
     * 2) example schema file:
      {
        "type": "array",
        "options": {},
        "items": {
            "type": "object",
            "properties": {
                "example": {
                    "title": "Example",
                    "type": "string",
                    "default": "default example text"
                  }
            }
          }

      }
     * 3):
      $fielder->json('Parameters'),
      $fielder->json('Parameters')->compact()->nolabel(),
     */
    public function json(
        $name,
        $title = null,
        $options = [],
        $value = '{}',
        $form = null,
        $schema = '{}',
    ) {
        $this->existenceCheck($name);

        return JSONEditorField::create(
            $name,
            $title,
            $this->parent,
            $options,
            $value,
            $form,
            $schema,
        );
    }

    /**
     * DB Type:
        'Video' => 'Video',

     * See https://github.com/goldfinch/video-field
     */
    public function video($name, $title = null, $value = '', $static = false)
    {
        $this->existenceCheck($name);

        return VideoField::create($this->parent, $name, $title, $value, $static);
    }

    /**
     * DB Type:
        'Place' => 'Place',
     * Available methods:
     * .env required:
        APP_GOOGLE_MAPS_KEY=''

     * See https://github.com/goldfinch/google-fields
     */
    public function place($name, $title = null, $value = '')
    {
        $this->existenceCheck($name);

        return PlaceField::create($name, $title, $value);
    }

    /**
     * DB Type:
        'Map' => 'Map',
     * Available methods:
     * .env required:
        APP_GOOGLE_MAPS_KEY=''

     * See https://github.com/goldfinch/google-fields
     */
    public function map($name, $title = null, $value = '')
    {
        $this->existenceCheck($name);

        return MapField::create($name, $title, $value);
    }

    /**
     * DB Type: gorriecoe\Link\Models\Link
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $fielder->linkRel('ALink', 'Link'),
     */
    public function linkRel($name, $title = null, $linkConfig = [])
    {
        $this->fields->removeByName($name . 'ID');

        $this->existenceCheck($name);

        return LinkField::create($name, $title, $this->parent, $linkConfig);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_one
     * Available methods:
     */
    public function link($name, $title = null, $value = null)
    {
        $this->fields->removeByName($name . 'ID');

        $this->existenceCheck($name);

        return AnyField::create($name, $title, $value);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     *
     * * Required $has_one on SilverStripe\LinkField\Models\Link
     * eg:
      private static $has_one = [
          'Page' => \Page::class,
      ];
     */
    public function links($name, $title = null, SS_List $dataList = null)
    {
        // $this->fields->removeByName($name . 'ID');

        $this->existenceCheck($name);

        return ManyAnyField::create($name, $title, $dataList);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function linkSS($name, $title = null, $value = null)
    {
        // $this->fields->removeByName($name . 'ID');

        $this->existenceCheck($name);

        return LinkSSField::create($name, $title, $value);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     */
    // public function sslinks($name, $title = null, SS_List $dataList = null)
    // {
    //     // $this->fields->removeByName($name . 'ID');

    //     return MultiLinkField::create($name, $title, $dataList);
    // }

    /**
     * DB Type: *
     * Available methods: setMode() setTheme()
     */
    public function code(
        $name,
        $title = null,
        $value = null,
        $mode = 'ace/mode/html',
        $theme = 'ace/theme/github',
    ) {
        $this->existenceCheck($name);

        $field = CodeEditorField::create($name, $title, $value);
        $field->setMode($mode);
        $field->setTheme($theme);

        return $field;
    }

    /**
     * DB Type: -
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function tag(
        $name,
        $title = null,
        $source = [],
        $value = null,
        $titleField = 'Title',
    ) {
        $relation = $this->parent->getRelationType($name);

        if (
            !in_array($relation, ['has_many', 'many_many', 'belongs_many_many'])
        ) {
            return $this->returnError(
                $name,
                $name .
                    ': <b>multiSelect</b> is only for <b>many-many</b> relationship',
            );
        }

        if (empty($source)) {
            $this->lookForSourceObject($name, $title, $source);

            if (is_string($source)) {
                $source = $source::get();
                $value = $this->parent->$name();
            }
        }

        $this->existenceCheck($name);

        return TagField::create($name, $title, $source, $value, $titleField);
    }

    /**
     * DB Type: -
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     *
     * 1)
        use LittleGiant\SilverStripeImagePoints\DataObjects\Point;
        use SilverStripe\Assets\Image;

        private static $has_one = [
            'Image' => Image::class,
        ];

        private static $has_many = [
            'ImagePoints' => Point::class . '.PointOf',
        ];

        private static $owns = [
            'Image',
        ];
     * 2)
        LittleGiant\SilverStripeImagePoints\DataObjects\Point:
          image_width: 1918
          image_height: 822
     * Code example:
        $fielder->points('ImagePoints'),
     */
    public function points(
        $name,
        $title = null,
        $source = [],
        $gridconfig = null,
    ) {
        if (!$source) {
            $relation = $this->parent->getRelationType($name);

            if (
                in_array($relation, [
                    'has_many',
                    'many_many',
                    'belongs_many_many',
                ])
            ) {
                $source = $this->parent->$name();
            }
        }

        $this->existenceCheck($name);

        $grid = $this->grid($name, $title, $source, $gridconfig)
            ->components(['add', 'detail-form', 'delete', 'edit'])
            ->build();

        return $grid;
    }

    // public function point($name, $title = null, $value = '', $image = '', $width = 0, $height = 0)
    // {
    //     return PointField::create($name, $title, $value, $image, $width, $height);
    // }

    /**
     * DB Type: -
     * Available methods:
     */
    public function wrapper(...$children)
    {
        return Wrapper::create($children);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->autocomplete('Page', 'Page', '', Page::class, 'Title'),
     */
    public function autocomplete(
        $name,
        $title = null,
        $value = '',
        $sourceClass = null,
        $sourceFields = null,
    ) {
        // $this->lookForSourceObject($name, $title, $sourceClass);

        $this->existenceCheck($name);

        return AutoCompleteField::create(
            $name,
            $title,
            $value,
            $sourceClass,
            $sourceFields,
        );
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->stringTag('Varchar', 'Varchar', CardItem::get()),
     */
    public function stringTag($name, $title = null, $source = [], $value = null)
    {
        // $this->lookForSource($name, $title, $source);

        $this->existenceCheck($name);

        return StringTagField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $fielder->readonlyTag('Text', 'Text', [1 => 'Tag 1', 2 => 'Tag 2']),
     */
    // public function readonlyTag($name, $title = '', $source = [], $value = null, $titleField = 'Title')
    // {
    //     return ReadonlyTagField::create($name, $title, $source, $value, $titleField);
    // }

    /**
     * DB Type: -
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $fielder->imageCoords('Image', 'Focus Point', true),
        $fielder->imageCoords('Image', 'Focus Point'),
     */
    public function imageCoords(
        $name,
        $title,
        $onlyCanvas = false,
        $cssGrid = false,
        $image = null,
        $XFieldName = null,
        $YFieldName = null,
        $xySumFieldName = null,
        $width = null,
        $height = null,
    ) {
        // TODO: ImageCoordsField::create($this, 'Image'),

        if (!$image) {
            $relation = $this->parent->getRelationType($name);

            if (in_array($relation, ['has_one'])) {
                $image = $this->parent->$name();
            }

            $XFieldName = $name . '-_1_-FocusPointX';
            $YFieldName = $name . '-_1_-FocusPointY';
            $xySumFieldName = 'filename';
            $width = $image->getWidth();
            $height = $image->getHeight();
        }

        $this->existenceCheck($XFieldName);
        $this->existenceCheck($YFieldName);
        $this->existenceCheck($xySumFieldName);

        return ImageCoordsField::create(
            $title,
            $XFieldName,
            $YFieldName,
            $xySumFieldName,
            $image,
            $width,
            $height,
            $cssGrid,
            $onlyCanvas,
        );
    }

    // TODO
    public function imageEditableGrid()
    {
        // GridField::create('ImageAttributes', 'Images', $this->Images(), GridFieldManyManyFocusConfig::create()),
    }

    /**
     * DB Type: -
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $fielder->focusPoint('FocusPoint', 'Focus Point', $this->Image()),
     */
    // public function focusPoint(string $name, ?string $title = null, ?Image $image = null)
    // {
    //     return FocusPointField::create($name, $title, $image);
    // }

    // public function previewImage($name, $title = null, $value = null)
    // {
    //     return PreviewImageField::create($name, $title, $value);
    // }

    // public function anchorSelector($name, $title = null, $value = '', $maxLength = null, $form = null)
    // {
    //     return AnchorSelectorField::create($name, $title, $value, $maxLength, $form);
    // }

    /**
     * DB Type: *
     * Available methods:
     */
    public function siteTreeURLSegment(
        $name,
        $title = null,
        $value = '',
        $maxLength = null,
        $form = null,
    ) {
        $this->existenceCheck($name);

        return SiteTreeURLSegmentField::create(
            $name,
            $title,
            $value,
            $maxLength,
            $form,
        );
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function htmlReadonly($name, $title = null, $value = null)
    {
        $this->existenceCheck($name);

        return HTMLReadonlyField::create($name, $title, $value);
    }

    // public function diff($name, $title = null, $value = null)
    // {
    //     return DiffField::create($name, $title, $value);
    // }

    // public function hasOneUpload(UploadField $original)
    // {
    //     return HasOneUploadField::create($original);
    // }

    /**
     * DB Type:
     *
        'MyText' => EncryptedDBText::class,
        'MyHTMLText' => EncryptedDBHTMLText::class,
        'MyVarchar' => EncryptedDBVarchar::class,
        'MyNumber' => EncryptedNumberField::class,
        'MyIndexedVarchar' => EncryptedDBField::class,
     * Available methods:
     *
     */
    public function encrypt($name)
    {
        // https://github.com/goldfinch/silverstripe-encrypt/

        if (EncryptHelper::isEncryptedField(get_class($this->parent), $name)) {
            $this->parent->$name = $this->parent->dbObject($name)->getValue();
        }

        $this->existenceCheck($name);

        return $this->field($name);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function country(
        $name,
        $title = null,
        $source = [],
        $value = '',
        $form = null,
    ) {
        $this->existenceCheck($name);

        return CountryDropdownField::create(
            $name,
            $title,
            $source,
            $value,
            $form,
        );
    }

    /**
     * DB Type: Goldfinch\IconField\ORM\FieldType\DBIcon
     * Available methods:
     * 1) required .yml config (see goldfinch/icon-field/README.md)
     */
    public function icon($set, $name, $title = null, $value = '')
    {
        $this->existenceCheck($name);

        return IconField::create($set, $name, $title, $value);
    }

    /**
     * DB Type: Phone
     * Available methods:
        $Phone.International
        $Phone.National
        $Phone.E164
        $Phone.RFC3966
        $Phone.URL
     */
    public function phone($name, $title = null, $options = [])
    {
        if (!$this->isDBType($name, DBPhone::class)) {
            return $this->returnTypeError($name, 'phone');
        }

        $this->existenceCheck($name);

        $field = DBPhone::create($name, $options);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type:
     *
        'Image' => Image::class,

        'Images' => Image::class,
     * Available methods:
     *
     * Code example:
        $fielder->mediaSelect('Image', 'Images'),
     */
    public function mediaSelect($name, $relationList, $title = null)
    {
        if (is_string($relationList)) {
            $relation = $this->parent->getRelationType($relationList);

            if (
                in_array($relation, [
                    'has_many',
                    'many_many',
                    'belongs_many_many',
                ])
            ) {
                $relationList = $this->parent->$relationList();
            }
        }

        $nameRelation = $name . 'ID';

        $this->existenceCheck($nameRelation);

        return ImageSelectionField::create(
            $nameRelation,
            $title ?? $name,
        )->setImageList($relationList);
    }

    private function isDBType($name, $type)
    {
        $object = $this->parent->dbObject($name);

        return $object && get_class($this->parent->dbObject($name)) == $type;
    }

    private function returnTypeError($name, $type)
    {
        return $this->literal(
            $name . '_error',
            '<div class="alert alert-warning"><b>' .
                $name .
                '</b> is not type of ' .
                $type .
                '</div>',
        );
    }

    private function returnError($name, $message)
    {
        return $this->literal(
            $name . '_error',
            '<div class="alert alert-warning">' . $message . '</div>',
        );
    }

    private function existenceCheck($name)
    {
        if ($name && $this->dataField($name)) {
            $this->remove($name);
        }
    }
}
