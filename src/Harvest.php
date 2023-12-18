<?php

namespace Goldfinch\Harvest;

use Goldfinch\Harvest\Grid;
use SilverStripe\Forms\Tab;
use SilverStripe\ORM\SS_List;
use SilverStripe\Assets\Image;
use SilverStripe\Forms\TabSet;
use BetterBrief\GoogleMapField;
use SilverStripe\ORM\DataObject;
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
use Goldfinch\Icon\Forms\IconFileField;
use Goldfinch\Icon\Forms\IconFontField;
use SilverStripe\ORM\FieldType\DBFloat;
use SilverStripe\AnyField\Form\AnyField;
use SilverStripe\Forms\CheckboxSetField;
use SilverStripe\ORM\FieldType\DBBigInt;
use SilverStripe\ORM\FieldType\DBDouble;
use SilverStripe\ORM\FieldType\DBLocale;
use SilverStripe\Forms\HTMLReadonlyField;
use SilverStripe\Forms\SingleLookupField;
use SilverStripe\Forms\TreeDropdownField;
use SilverStripe\ORM\FieldType\DBDecimal;
use SilverStripe\TagField\StringTagField;
use JonoM\FocusPoint\Forms\FocusPointField;
use PhpTek\JSONText\ORM\FieldType\JSONText;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\SelectionGroup_Item;
use SilverStripe\TagField\ReadonlyTagField;
use UncleCheese\DisplayLogic\Forms\Wrapper;
use SilverStripe\AnyField\Form\ManyAnyField;
use SilverStripe\Forms\GroupedDropdownField;
use SilverStripe\Forms\ToggleCompositeField;
use SilverStripe\Forms\TreeMultiselectField;
use SilverStripe\ORM\FieldType\DBPercentage;
use SilverStripe\Security\Confirmation\Item;
use SilverShop\HasOneField\HasOneButtonField;
use gorriecoe\LinkField\Forms\HasOneLinkField;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\ConfirmedPasswordField;
use TractorCow\AutoComplete\AutoCompleteField;
use Goldfinch\JSONEditor\Forms\JSONEditorField;
use SilverStripe\CMS\Forms\AnchorSelectorField;
use SilverStripe\LinkField\Form\MultiLinkField;
use SilverStripe\VersionedAdmin\Forms\DiffField;
use Heyday\ColorPalette\Fields\ColorPaletteField;
use SilverStripe\Forms\GridField\GridFieldConfig;
use SilverStripe\Forms\HTMLEditor\HTMLEditorField;
use SilverStripe\CMS\Forms\SiteTreeURLSegmentField;
use SilverStripe\AssetAdmin\Forms\PreviewImageField;
use Goldfinch\FocusPointExtra\Forms\ImageCoordsField;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use Innoweb\InternationalPhoneNumberField\ORM\DBPhone;
use KevinGroeger\CodeEditorField\Forms\CodeEditorField;
use Kinglozzer\MultiSelectField\Forms\MultiSelectField;
use RyanPotter\SilverStripeColorField\Forms\ColorField;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use Heyday\ColorPalette\Fields\GroupedColorPaletteField;
use Goldfinch\FocusPointExtra\Forms\UploadFieldWithExtra;
use LittleGiant\SilverStripeImagePoints\Forms\PointField;
use NSWDPC\Forms\ImageSelectionField\ImageSelectionField;
use SilverStripe\LinkField\Form\LinkField as SSLinkField;
use Dynamic\CountryDropdownField\Fields\CountryDropdownField;
use Goldfinch\FocusPointExtra\Forms\SortableUploadFieldWithExtra;

class Harvest
{
    private $fields = null;
    private $initialFields = null;
    private $allFieldsCleared = false;
    private $requiredFields = [];
    private $error = null;

    private $parent = null;

    public function __construct($fields, $parent)
    {
        $this->fields = $fields;
        $this->initialFields = clone $this->fields;
        $this->parent = $parent;
    }

    public function field($name, $title = null)
    {
        return $this->parent->dbObject($name)->scaffoldFormField($title);
    }

    public function clear($fields)
    {
        $this->fields->removeByName($fields);
    }

    public function fields($fieldsList)
    {
        foreach ($fieldsList as $tab => $list)
        {
            $this->fields->addFieldsToTab($tab, $list);
        }

        return $this->fields;
    }

    public function dataField($name)
    {
        if ($this->allFieldsCleared)
        {
            return $this->initialFields->dataFieldByName($name);
        }
        else
        {
            return $this->fields->dataFieldByName($name);
        }
    }

    public function clearAll()
    {
        foreach ($this->fields->flattenFields() as $field)
        {
            if (!in_array(get_class($field), [
              Tab::class,
              TabSet::class,
            ]))
            {
                $this->fields->removeByName($field->getName());
            }
        }

        $this->allFieldsCleared = true;
    }

    public function required($fields)
    {
        $this->setRequiredFields($fields);
    }

    public function setRequiredFields($fields)
    {
        $this->requiredFields = $fields;
    }

    public function getRequiredFields()
    {
        return $this->requiredFields;
    }

    public function addError($error)
    {
        $this->error = $error;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getFields()
    {
        return $this->fields;
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
        return CheckboxField::create($name, $title, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function dropdown($name, $title = null, $source = [], $value = null)
    {
        // if (empty($source))
        // {
        //     $relation = $this->parent->getRelationType($name);

        //     if ($relation)
        //     {
        //         $source = $this->parent->$name()->map();
        //     }
        // }

        return DropdownField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function readonly($name, $title = null, $value = null)
    {
        return ReadonlyField::create($name, $title, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function text($name, $title = null, $value = null)
    {
        return TextareaField::create($name, $title, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function string($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return TextField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function password($name, $title = null, $value = '')
    {
        return PasswordField::create($name, $title, $value);
    }

    /* ------- [Actions] ------- */

    /**
     * --
     */
    public function action($action, $title = "", $form = null)
    {
        return FormAction::create($action, $title, $form);
    }

    /* ------- [Formatted input] ------- */

    /**
     * DB Type: *
     * Available methods:
     */
    public function passwordConfirmed($name, $title = null, $value = "", $form = null, $showOnClick = false, $titleConfirmField = null)
    {
        return ConfirmedPasswordField::create($name, $title, $value, $form, $showOnClick, $titleConfirmField);
    }

    /**
     * DB Type: Currency
     * Available methods:
     */
    public function currency($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return CurrencyField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: Date
     * Available methods:
     */
    public function date($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return DateField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: Datetime
     * Available methods:
     */
    public function datetime($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return DatetimeField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function email($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return EmailField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->groupedDropdown('Name', 'Title', [
            'numbers' => [1 => 1, 2 => 2],
            'letters' => [1 => 'A', 2 => 'B'],
        ]),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function groupedDropdown($name, $title = null, $source = [], $value = null)
    {
        return GroupedDropdownField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function html($name, $title = null, $value = '', $config = null)
    {
        return HTMLEditorField::create($name, $title, $value, $config);
    }

    /**
     * DB Type: Money
     * Available methods:
     */
    public function money($name, $title = null, $value = "")
    {
        return MoneyField::create($name, $title, $value);
    }

    /**
     * DB Type: Decimal | Float | Int
     * Available methods:
     */
    public function numeric($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return NumericField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->optionset('Name', 'Title', [1 => 'Option 1', 2 => 'Option 2']),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function optionset($name, $title = null, $source = [], $value = null)
    {
        return OptionsetField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->selectionGroup('Name', [
            $harvest->selectionGroupItem(
                'one',
                $harvest->literal('one', 'one view'),
                'one title'
            ),
            $harvest->selectionGroupItem(
                'two',
                $harvest->literal('two', 'two view'),
                'two title'
            ),
        ]),
     */
    public function selectionGroup($name, $items, $value = null)
    {
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
    public function time($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return TimeField::create($name, $title, $value, $maxLength, $form);
    }

    /* ------- [Structure] ------- */

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $harvest->composite([
            $harvest->string('Title'),
            $harvest->html('Text'),
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
        $harvest->group('Group Title', [
            $harvest->string('Title'),
            $harvest->html('Text'),
        ]),
     */
    public function group($titleOrField = null, $otherFields = null)
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
        ...$harvest->list([
            $harvest->string('Title'),
            $harvest->html('Text'),
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
        $harvest->tab('Primary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
     */
    public function tab($name, $titleOrField = null, $fields = null)
    {
        return Tab::create($name, $titleOrField, $fields);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $harvest->tabSet('MyTabSetName',
            $harvest->tab('Primary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
            $harvest->tab('Secondary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Banshee</b>')),
        ),
     */
    public function tabSet($name, $titleOrTab = null, $tabs = null)
    {
        return TabSet::create($name, $titleOrTab, $tabs);
    }

    /**
     * DB Type: -
     * Available methods:
     *
     * Code example:
        $harvest->toggleComposite('MyToggle', 'Toggle', [
            $harvest->string('Title'),
            $harvest->text('Text')
        ]),
     */
    public function toggleComposite($name, $title, $children)
    {
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
        return UploadField::create($name, $title, $items);
    }

    /**
     * DB Type: -
     * Allowed relations: has_one | has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function file($name, $title = null, $value = null)
    {
        return FileField::create($name, $title, $value);
    }

    /* ------- [Relations] ------- */

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->checkboxSet('List', 'List', [1 => 'Set 1', 2 => 'Set 2']),
     * Code example:
        $source = FooBar::get()->map()
     */
    public function checkboxSet($name, $title = null, $source = [], $value = null)
    {
        return CheckboxSetField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $harvest->dropdownTree('PageID', 'Page', Page::class),
     */
    public function dropdownTree($name, $title = null, $sourceObject = null, $keyField = 'ID', $labelField = 'TreeTitle', $showSearch = true)
    {
        return TreeDropdownField::create($name, $title, $sourceObject, $keyField, $labelField, $showSearch);
    }

    /**
     * (!) only for Groups
     *
     * DB Type: *
     * Allowed relations: has_many | many_many
     * Available methods:
     */
    public function treeMultiSelect($name, $title = null, $sourceObject = Group::class, $keyField = "ID", $labelField = "Title")
    {
        return TreeMultiselectField::create($name, $title, $sourceObject, $keyField, $labelField);
    }


    /**
     * DB Type: *
     * Allowed relations: has_many | many_many
     * Available methods:
     *
     * Code example:
        $harvest->grid('Services', 'Services')->build(),

        $harvest->grid('Services', 'Services', $this->Services())->build(),

        $harvest->grid('Cards', 'Cards')
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
    public function grid($name, $title = null, SS_List $dataList = null, GridFieldConfig $config = null)
    {
        $grid = new Grid($this->fields, $this->parent);
        $grid->init($name, $title, $dataList, $config);

        return $grid;
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->listbox('List', 'List', [1 => 'Option 1', 2 => 'Option 2']),
     */
    public function listbox($name, $title = '', $source = [], $value = null, $size = null)
    {
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
        return HeaderField::create($name, $title, $headingLevel);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function hidden($name, $title = null, $value = null)
    {
        return HiddenField::create($name, $title, $value);
    }

    /**
     * DB Type: -
     * Available methods:
     */
    public function label($name, $title = null)
    {
        return LabelField::create($name, $title);
    }

    /**
     * DB Type: -
     * Available methods:
     */
    public function literal($name, $content)
    {
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
        $harvest->nullable($harvest->string('Text')),
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
     * DB Type: *
     * Available methods:
     */
    public function decimal($name, $title = null, $wholeSize = 9, $decimalSize = 2, $defaultValue = 0)
    {
        if (!$this->isDBType($name, DBDecimal::class))
        {
            return $this->returnError($name, 'decimal');
        }

        // public function decimal($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(2);

        $field = new DBDecimal($name, $wholeSize, $decimalSize, $defaultValue);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function double($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBDouble::class))
        {
            return $this->returnError($name, 'double');
        }

        // public function double($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form);

        $field = new DBDouble($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function float($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBFloat::class))
        {
            return $this->returnError($name, 'float');
        }

        // public function float($name, $title = null, $value = '', $maxLength = null, $form = null)
        // return NumericField::create($name, $title, $value, $maxLength, $form)->setScale(null);

        $field = new DBFloat($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Year
     * Available methods:
     */
    public function year($name, $title = null, $options = [])
    {
        if (!$this->isDBType($name, DBYear::class))
        {
            return $this->returnError($name, 'year');
        }

        $field = new DBYear($name, $options = []);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Percentage | Percentage(6)
     * Available methods:
     */
    public function percentage($name, $title = null, $precision = 4)
    {
        if (!$this->isDBType($name, DBPercentage::class))
        {
            return $this->returnError($name, 'percentage');
        }

        $field = new DBPercentage($name, $precision);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Int
     * Available methods:
     */
    public function int($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBInt::class))
        {
            return $this->returnError($name, 'int');
        }

        $field = new DBInt($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: BigInt
     * Available methods:
     */
    public function bigInt($name, $title = null, $defaultVal = 0)
    {
        if (!$this->isDBType($name, DBBigInt::class))
        {
            return $this->returnError($name, 'int');
        }

        $field = new DBBigInt($name, $defaultVal);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Locale
     * Available methods:
     */
    public function locale($name, $title = null, $size = 16)
    {
        if (!$this->isDBType($name, DBLocale::class))
        {
            return $this->returnError($name, 'int');
        }

        $field = new DBLocale($name, $size);
        return $field->scaffoldFormField($title);
    }

    /**
     * DB Type: Enum("Apple,Orange,Kiwi", "Kiwi")
     * Available methods:
     */
    public function enum($name)
    {
        if (!$this->isDBType($name, DBEnum::class))
        {
            return $this->returnError($name, 'enum');
        }

        // public function enum($name, $title = null, $source = [], $value = null)
        // public function enum($name, $title = null, $enum = null, $default = 0, $options = [])
        // $field = new DBEnum($name, $enum, $default, $options);
        // return $field->scaffoldFormField($title);
        // return DropdownField::create($name, $title, $source, $value);

        return $this->field($name);
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

    public function hasOneButton($relationName, $fieldName = null, $title = null, GridFieldConfig $customConfig = null, $useAutocompleter = true)
    {
        return HasOneButtonField::create($this->parent, $relationName, $fieldName, $title, $customConfig, $useAutocompleter);
    }

    /**
     * DB Type: -
     * Allowed relations: many_many
     * Available methods:
     *
     * Code example:
        $harvest->multiSelect('Services', 'Services', 'SortExtra'),
     */
    public function multiSelect($name, $title = null, $sort = false, $source = null, $titleField = 'Title')
    {
        return MultiSelectField::create($name, $title, $this->parent, $sort, $source, $titleField);
    }

    /**
     * DB Type: -
     * Allowed relations: has_one | has_many | many_many | belongs_many_many
     * Available methods:
     *
     * Code example:
        ...$harvest->media('Image'),
     */
    public function media($name, $title = null)
    {
        return UploadFieldWithExtra::create($name, $title, $this->fields, $this->parent)->getFields();
    }

    /**
     * DB Type: -
     * Allowed relations: has_many | many_many
     * Available methods:
     *
     * Code example:
        ...$harvest->mediaSortable('Images'),
     */
    public function mediaSortable($name, $title = null)
    {
        return SortableUploadFieldWithExtra::create($name, $title, $this->fields, $this->parent)->getFields();
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->color('Color', 'Color', ['yellow' => '#fee12f', 'pink' => '#eb83ad', 'green' => '#70cd77']),
     */
    public function color($name, $title = null, $source = [], $value = null)
    {
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
        return ColorField::create($name, $title, $value, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->colorGroup('Color', 'Color', ['Primary' => ['yellow' => '#fee12f', 'pink' => '#eb83ad'], 'Secondary' => ['green' => '#70cd77']]),
     */
    public function colorGroup($name, $title = null, $source = [], $value = null)
    {
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
      $harvest->json('Parameters'),
     */
    public function json($name, $title = null, $options = [], $value = '{}', $form = null, $schema = '{}')
    {
        return JSONEditorField::create($name, $title, $this->parent, $options, $value, $form, $schema);
    }

    /**
     * DB Type:
        'Latitude' => 'Varchar',
        'Longitude' => 'Varchar',
        'Zoom' => 'Int',
     * Available methods:
     * .env required:
        APP_GOOGLE_MAPS_KEY=""
     * Code example:
        $harvest->map('Map', [
            'show_search_box' => false,
            'map' => [
                'mapTypeId' => 'satellite',
            ]
        ]),
     */
    public function map($title, $options = [])
    {
        // $this->fields->removeByName([
        //     'Latitude',
        //     'Longitude',
        //     'Zoom',
        // ]);

        return GoogleMapField::create($this->parent, $title, $options);
    }

    /**
     * DB Type: gorriecoe\Link\Models\Link
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $harvest->link('ALink', 'Link'),
     */
    public function link($name, $title = null, $linkConfig = [])
    {
        $this->fields->removeByName($name . 'ID');

        return LinkField::create($name, $title, $this->parent, $linkConfig);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_one
     * Available methods:
     */
    public function anyLink($name, $title = null, $value = null)
    {
        // $this->fields->removeByName($name . 'ID');

        return AnyField::create($name, $title, $value);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function anyLinks($name, $title = null, SS_List $dataList = null)
    {
        // $this->fields->removeByName($name . 'ID');

        return ManyAnyField::create($name, $title, $dataList);
    }

    /**
     * DB Type: SilverStripe\LinkField\Models\Link;
     * Allowed relations: has_many | many_many | belongs_many_many
     * Available methods:
     */
    public function sslink($name, $title = null, $value = null)
    {
        // $this->fields->removeByName($name . 'ID');

        return SSLinkField::create($name, $title, $value);
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
    public function code($name, $title = null, $value = null, $mode = 'ace/mode/html', $theme = 'ace/theme/github')
    {
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
    public function tag($name, $title = null, $source = [], $value = null, $titleField = 'Title')
    {
        if (!$source)
        {
            $relation = $this->parent->getRelationType($name);

            if (in_array($relation, ['has_many', 'many_many', 'belongs_many_many']))
            {
                $source = $this->parent->$name();
            }
        }

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
        $harvest->points('ImagePoints'),
     */
    public function points($name, $title = null, $source = [], $gridconfig = null)
    {
        if (!$source)
        {
            $relation = $this->parent->getRelationType($name);

            if (in_array($relation, ['has_many', 'many_many', 'belongs_many_many']))
            {
                $source = $this->parent->$name();
            }
        }

        $grid = $this->grid($name, $title, $source, $gridconfig);

        // TODO: add components through Grid class instead
        $grid->getConfig()->addComponents(
            GridFieldAddNewButton::create(),
            GridFieldDetailForm::create(),
            GridFieldDeleteAction::create(),
            GridFieldEditButton::create(),
        );

        return $grid;
    }

    // public function point($name, $title = null, $value = "", $image = '', $width = 0, $height = 0)
    // {
    //     return PointField::create($name, $title, $value, $image, $width, $height);
    // }

    /**
     * DB Type: -
     * Available methods:
     */
    public function wrapper($children = null)
    {
        return Wrapper::create($children);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->autocomplete('Page', 'Page', '', Page::class, 'Title'),
     */
    public function autocomplete($name, $title = null, $value = '', $sourceClass = null, $sourceFields = null)
    {
        return AutoCompleteField::create($name, $title, $value, $sourceClass, $sourceFields);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->stringTag('Text', 'Text', [1 => 'Tag 1', 2 => 'Tag 2']),
     */
    public function stringTag($name, $title = null, $source = [], $value = null)
    {
        return StringTagField::create($name, $title, $source, $value);
    }

    /**
     * DB Type: *
     * Available methods:
     *
     * Code example:
        $harvest->readonlyTag('Text', 'Text', [1 => 'Tag 1', 2 => 'Tag 2']),
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
        $harvest->imageCoords('Image', 'Focus Point', true),
        $harvest->imageCoords('Image', 'Focus Point'),
     */
    public function imageCoords($name, $title, $onlyCanvas = false, $cssGrid = false, $image = null, $XFieldName = null, $YFieldName = null, $xySumFieldName = null, $width = null, $height = null)
    {
        if (!$image)
        {
            $relation = $this->parent->getRelationType($name);

            if (in_array($relation, ['has_one']))
            {
                $image = $this->parent->$name();
            }

            $XFieldName = $name .'-_1_-FocusPointX';
            $YFieldName = $name .'-_1_-FocusPointY';
            $xySumFieldName = 'filename';
            $width = $image->getWidth();
            $height = $image->getHeight();
        }

        return ImageCoordsField::create($title, $XFieldName, $YFieldName, $xySumFieldName, $image, $width, $height, $cssGrid, $onlyCanvas);
    }

    /**
     * DB Type: -
     * Allowed relations: has_one
     * Available methods:
     *
     * Code example:
        $harvest->focusPoint('FocusPoint', 'Focus Point', $this->Image()),
     */
    // public function focusPoint(string $name, ?string $title = null, ?Image $image = null)
    // {
    //     return FocusPointField::create($name, $title, $image);
    // }

    /**
     * DB Type: -
     * Allowed relations: has_one
     * Available methods:
     */
    public function hasOneLink($relationName, $title = null, $linkConfig = [], $useAutocompleter = false)
    {
        return HasOneLinkField::create($this->parent, $relationName, $title, $linkConfig, $useAutocompleter);
    }

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
    public function siteTreeURLSegment($name, $title = null, $value = '', $maxLength = null, $form = null)
    {
        return SiteTreeURLSegmentField::create($name, $title, $value, $maxLength, $form);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function htmlReadonly($name, $title = null, $value = null)
    {
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

        if (EncryptHelper::isEncryptedField(get_class($this->parent), $name))
        {
            $this->parent->$name = $this->parent->dbObject($name)->getValue();
        }

        return $this->field($name);
    }

    /**
     * DB Type: *
     * Available methods:
     */
    public function country($name, $title = null, $source = [], $value = '', $form = null)
    {
        return CountryDropdownField::create($name, $title, $source, $value, $form);
    }

    /**
     * DB Type: Goldfinch\Icon\ORM\FieldType\DBIcon
     * Available methods:
     * 1) required .yml config (see goldfinch/icon/README.md)
     */
    public function iconFile($name, $title = null, $sourceFolder = null)
    {
        return IconFileField::create($name, $title, $sourceFolder);
    }

    /**
     * DB Type: Goldfinch\Icon\ORM\FieldType\DBIcon
     * Available methods:
     * 1) required .yml config (see goldfinch/icon/README.md)
     */
    public function iconFont($name, $title = null)
    {
        return IconFontField::create($name, $title);
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
        if (!$this->isDBType($name, DBPhone::class))
        {
            return $this->returnError($name, 'phone');
        }

        $field = new DBPhone($name, $options);
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
        $harvest->mediaSelect('Image', 'Images'),
     */
    public function mediaSelect($name, $relationList, $title = null)
    {
        if (is_string($relationList))
        {
            $relation = $this->parent->getRelationType($relationList);

            if (in_array($relation, ['has_many', 'many_many', 'belongs_many_many']))
            {
                $relationList = $this->parent->$relationList();
            }
        }

        return ImageSelectionField::create($name . 'ID', $title ?? $name)->setImageList($relationList);
    }

    private function isDBType($name, $type)
    {
        return get_class($this->parent->dbObject($name)) == $type;
    }

    private function returnError($name, $type)
    {
        return $this->literal($name . '_error', '<span style="color: red"><b>' . $name . '</b> is not type of ' . $type . '</span>');
    }
}
