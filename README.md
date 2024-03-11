
# 🦅 Fielder, fields manager for Silverstripe

[![Silverstripe Version](https://img.shields.io/badge/Silverstripe-5.1-005ae1.svg?labelColor=white&logoColor=ffffff&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDEuMDkxIDU4LjU1NSIgZmlsbD0iIzAwNWFlMSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBkPSJNNTAuMDE1IDUuODU4bC0yMS4yODMgMTQuOWE2LjUgNi41IDAgMCAwIDcuNDQ4IDEwLjY1NGwyMS4yODMtMTQuOWM4LjgxMy02LjE3IDIwLjk2LTQuMDI4IDI3LjEzIDQuNzg2czQuMDI4IDIwLjk2LTQuNzg1IDI3LjEzbC02LjY5MSA0LjY3NmM1LjU0MiA5LjQxOCAxOC4wNzggNS40NTUgMjMuNzczLTQuNjU0QTMyLjQ3IDMyLjQ3IDAgMCAwIDUwLjAxNSA1Ljg2MnptMS4wNTggNDYuODI3bDIxLjI4NC0xNC45YTYuNSA2LjUgMCAxIDAtNy40NDktMTAuNjUzTDQzLjYyMyA0Mi4wMjhjLTguODEzIDYuMTctMjAuOTU5IDQuMDI5LTI3LjEyOS00Ljc4NHMtNC4wMjktMjAuOTU5IDQuNzg0LTI3LjEyOWw2LjY5MS00LjY3NkMyMi40My0zLjk3NiA5Ljg5NC0uMDEzIDQuMTk4IDEwLjA5NmEzMi40NyAzMi40NyAwIDAgMCA0Ni44NzUgNDIuNTkyeiIvPjwvc3ZnPg==)](https://packagist.org/packages/goldfinch/fielder)
[![Package Version](https://img.shields.io/packagist/v/goldfinch/fielder.svg?labelColor=333&color=F8C630&label=Version)](https://packagist.org/packages/goldfinch/fielder)
[![Total Downloads](https://img.shields.io/packagist/dt/goldfinch/fielder.svg?labelColor=333&color=F8C630&label=Downloads)](https://packagist.org/packages/goldfinch/fielder)
[![License](https://img.shields.io/packagist/l/goldfinch/fielder.svg?labelColor=333&color=F8C630&label=License)](https://packagist.org/packages/goldfinch/fielder) 

Fielder 🚜 is fields organizer that helps to simplify fields declaration and makes it easy to manage all in one place, keeping it clean with less code.

## Install

```bash
composer require goldfinch/fielder
```

## Usage

Fielder within the cms fields in `DataObject`

```php
use SilverStripe\ORM\DataObject;

MyAwesomeModel extends DataObject
{
    public function getCMSFields()
    {
        $fields = parent::getSettingsFields()->initFielder($this);
        
        $fielder = $fields->getFielder();

        $fielder->remove('Content');

        $fielder->required([
            'FirstName',
            'About',
        ]);

        $fielder->fields([
            'Root.Main' => [
                $fielder->string('FirstName', 'First name'),
                $fielder->text('About'),
                $fielder->html('Content'),
            ],
            'Root.Demo' => [
                $fielder->string('Headline'),
            ],
        ]);

        return $fields;
    }
}
```

Fielder within the settings fields in `SiteTree`

```php
use SilverStripe\CMS\Model\SiteTree;

MyAwesomePage extends SiteTree
{
    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields()->initFielder($this);

        $fielder = $fields->getFielder();
        
        $fielder->remove('ShowInMenus');

        return $fields;
    }
}
```

Fielder has its own validator method to ease validation process (see [validate method](https://github.com/goldfinch/fielder#list-of-available-methods)). Having said that the default SS validation method can still be presented and will be called alongside.

```php
use SilverStripe\ORM\DataObject;

MyAwesomeModel extends DataObject
{
    public function getCMSFields()
    {
        $fields = parent::getSettingsFields()->initFielder($this);
        
        $fielder = $fields->getFielder();
        
        // fielder validation
        $fielder->validate([
            'Email' => 'required|email'
        ]);

        return $fields;
    }

    // default SS validation
    public function validate()
    {
        $result = parent::validate();

        if ($this->Title != 'Home') {
            $result->addError('Title is invalid');
        }

        return $result;
    }
}
```

Fielder in extension

```php
use SilverStripe\ORM\DataExtension;

class MyExtension extends DataExtension
{
    public function updateCMSFields($fields)
    {
        $fields->initFielder($this->owner);

        $fielder = $fields->getFielder();

        $fielder->validate([
            'Email' => 'required|email',
        ]);
    }

    public function updateSettingsFields($fields)
    {
        $fields->initFielder($this->owner);

        $fielder = $fields->getFielder();

        $fielder->remove('ShowInMenus');
    }
}
```

## List of available methods

> declare fields in tabs
```php
$fielder->fields([
  'Root.Main' => [
    $fielder->string('Title'),
  ],
])
```

> declare fields removing previously declared fields
```php
$fielder->freshFields([
  'Root.Main' => [
    $fielder->string('Title'),
  ],
])
```

> insert after
```php
$fielder->insertAfter('MenuTitle', $fielder->string('Title'));

$fielder->insertAfter('MenuTitle', [
    $fielder->string('Title'),
    $fielder->text('Content'),
]);
```

> insert before
```php
$fielder->insertBefore('MenuTitle', $fielder->string('Title'));

$fielder->insertBefore('MenuTitle', [
    $fielder->string('Title'),
    $fielder->text('Content'),
]);
```

> reorder fields
```php
$fielder->reorder(['Content', 'MenuTitle']);
```

> add fields to tab
```php
$fielder->toTab('Root.Demo', [
    $fielder->string('Title'),
    $fielder->text('Content'),
]);
```

> get data field, same as `dataFieldByName`
```php
$fielder->dataField('Title');
```

> get field, same as `scaffoldFormField`
```php
$fielder->field('Title');
```

> required field

```php
$fielder->required('Title')
$fielder->required(['Title', 'Content']);

//through validate method (recommended)

$fielder->validate([
    'Title' => 'required',
]);
```

> remove specific fields
```php
$fielder->remove('Title');
```

> remove all fields 
```php
$fielder->removeAll();
```

> remove all fields within the class (ignores fields that were added through inherited or extended classes)
```php
$fielder->removeAllCurrent();
```

> remove fields in tab
```php
$fielder->removeFieldsInTab('Root.Main');
```

> add description field
```php
$fielder->description('Title', 'Some field description flies here');
// ..
$fielder->description([
    'Title' => 'Some field description flies here',
    'URLSegment' => '<strong style="color: red">Red field description</strong>'
]);
```

> disable field
```php
$fielder->disable('Title');
$fielder->disable('Title', false); // undisabled
// ..
$fielder->disable(['Title', 'Text']);
$fielder->disable(['Title', 'Text'], false); // undisabled all
```

> readonly field
```php
$fielder->readonly('Title');
$fielder->readonly('Title', false); // take off readonly
// ..
$fielder->readonly(['Title', 'Text']);
$fielder->readonly(['Title', 'Text'], false); // take off readonly
```

> add custom error
```php
$fielder->addError('Error message'); // error | required | bad | validation
$fielder->addError('Error message', 'info');
$fielder->addError('Error message', 'warning');
$fielder->addError('Error message', 'message');
$fielder->addError('<strong>Error</strong> message', 'good', null, 'html');
```

> validate fields

**Basic closure validation per each field**

```php
$fielder->validate([
    'Title' => function($value, $fail) {
        $max = 100;
        if (strlen($value) > $max) {
            $fail('The :attribute must not be over ' . $max . ' characters.');
        }
    }
]);
```

**Laravel approach (recommended)**

This package comes with Laravel validation components that are ready to use. For more info on what rules are available and how to use them, please refer to [this list](https://laravel.com/docs/10.x/validation#available-validation-rules)

```php
use Goldfinch\Illuminate\Rule;

$fielder->validate([
    'Title' => 'required|regex:/^[\d\s\+\-]+$/',
    'Email' => 'required|email',
    'Fruits' => ['required', Rule::in(['apple', 'orange', 'kiwi'])],
]);
```

You can also create a custom rule that will handle your specific validation logic. Use [**Taz**](https://github.com/goldfinch/taz)🌪️ to create a new rule.

eg:
```bash
php taz make:rule PhoneRule
```

and simply implement it in your validation rules:
```php
use App\Rules\PhoneRule;

$fielder->validate([
    'Phone' => ['required', new PhoneRule()],
]);
```

> display logic methods

**Basic coverage, not all functions are yet available.**

```php
$fielder->displayIf();
$fielder->displayUnless();
$fielder->hideIf();
$fielder->hideUnless();
```

examples:

```php
$fielder->toTab('Root.Demo', [
    $fielder->checkbox('ConditionalField'),
    $fielder->displayIf('ConditionalField', [ // isChecked
    // $fielder->displayIf('!Magic', [ // isNotChecked
    // $fielder->displayIf(['MagicString', '==', null], [ // isEmpty
    // $fielder->displayIf(['MagicString', '!=', null], [ // isNotEmpty
    // $fielder->displayIf(['MagicString', '==', 3], [ // isEqualTo
    // $fielder->displayIf(['MagicString', '!=', 3], [ // isNotEqualTo
    // $fielder->displayIf(['MagicString', '>', 3], [ // isGreaterThan
    // $fielder->displayIf(['MagicString', '<', 3], [ // isLessThan
        $fielder->string('Field1'),
        $fielder->string('Field2'),
        $fielder->string('Field3'),
    ])
]);
```

## List of available fields

### ✳️ General fields

#### checkbox

> Checkbox field

Class: `SilverStripe\Forms\CheckboxField`

Suitable DB Type: `Boolean`

```php
$fielder->checkbox($name, $title = null, $value = null)

// left aligned checkbox (wrapped in composite field)
$fielder->lineCheckbox($name, $title = null, $value = null)
```

#### dropdown

> Dropdown field

Class: `SilverStripe\Forms\DropdownField`

Suitable DB Type: `*`

```php
$fielder->dropdown($name, $title = null, $source = [], $value = null)
```

#### readonly

> Readonly field

Class: `SilverStripe\Forms\ReadonlyField`

Suitable DB Type: `*`

```php
$fielder->readonlyField($name, $title = null, $value = null)
```

#### text

> Textarea field

Class: `SilverStripe\Forms\TextareaField`

Suitable DB Type: `*`

```php
$fielder->text($name, $title = null, $value = null)
```

#### string

> Text field

Class: `SilverStripe\Forms\TextField`

Suitable DB Type: `Varchar`

```php
$fielder->string($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### password

> Password field

Class: `SilverStripe\Forms\PasswordField`

Suitable DB Type: `Varchar`

```php
$fielder->password($name, $title = null, $value = '')
```

#### action

> Action field

Class: `SilverStripe\Forms\FormAction`

```php
$fielder->action($action, $title = '', $form = null)
```

#### passwordConfirmed

> Password confirm field

Class: `SilverStripe\Forms\ConfirmedPasswordField`

Suitable DB Type: `*`

```php
$fielder->passwordConfirmed($name, $title = null, $value = '', $form = null, $showOnClick = false, $titleConfirmField = null)
```

#### currency

> Currency field

Class: `SilverStripe\Forms\CurrencyField`

Suitable DB Type: `Currency`

```php
$fielder->currency($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### date

> Date field

Class: `SilverStripe\Forms\DateField`

Suitable DB Type: `Date`

```php
$fielder->date($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### datetime

> Datetime field

Class: `SilverStripe\Forms\DatetimeField`

Suitable DB Type: `Datetime`

```php
$fielder->datetime($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### email

> Email field

Class: `SilverStripe\Forms\EmailField`

Suitable DB Type: `Varchar`

```php
$fielder->email($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### groupedDropdown

> Grouped dropdown field

Class: `SilverStripe\Forms\GroupedDropdownField`

Suitable DB Type: `*`

```php
$fielder-> groupedDropdown($name, $title = null, $source = [], $value = null)
// ..
$fielder->groupedDropdown('Name', 'Title', [
    'numbers' => [1 => 1, 2 => 2],
    'letters' => [1 => 'A', 2 => 'B'],
])
// $source = FooBar::get()->map()
```

#### html

> HTML Editor field

Class: `SilverStripe\Forms\HTMLEditor\HTMLEditorField`

Suitable DB Type: `HTMLText`

```php
$fielder->html($name, $title = null, $value = '', $config = null)
```

#### money

> Money field

Class: `SilverStripe\Forms\MoneyField`

Suitable DB Type: `Money`

```php
$fielder->money($name, $title = null, $value = '')
```

#### numeric

> Numeric field

Class: `SilverStripe\Forms\NumericField`

Suitable DB Type: `Decimal` `Float` `Int`

```php
$fielder->numeric($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### radio

> Radio field

Class: `SilverStripe\Forms\OptionsetField`

Suitable DB Type: `*`

```php
$fielder->radio($name, $title = null, $source = [], $value = null)
// ..
$fielder->radio('Name', 'Title', [1 => 'Option 1', 2 => 'Option 2'])
// $source = FooBar::get()->map()
```

#### selectionGroup

> Selection group field

Class: `SilverStripe\Forms\SelectionGroup`

Suitable DB Type: `*`

```php
$fielder->selectionGroup($name, $items, $value = null)
// ..
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
])
```

#### time

> Time field

Class: `SilverStripe\Forms\TimeField`

Suitable DB Type: `Time`

```php
$fielder->time($name, $title = null, $value = '', $maxLength = null, $form = null)
```

### ✳️ Structure fields

#### composite

> Composite field

Class: `SilverStripe\Forms\CompositeField`

```php
$fielder->composite($children = null)
// ..
$fielder->composite([
    $fielder->string('Title'),
    $fielder->html('Text'),
])
```

#### group

> Group field

Class: `SilverStripe\Forms\FieldGroup`

```php
$fielder->group($titleOrField = null, ...$otherFields)
// ..
$fielder->group(
    'Group name',
    $fielder->string('Title'),
    $fielder->html('Text'),
)
```

#### list

> List field. FYI: $fields is FieldList already. Using this field we store new FieldList in FieldList

Class: `SilverStripe\Forms\FieldList`

```php
$fielder->list($items = [])
// ..
...$fielder->list([
    $fielder->string('Title'),
    $fielder->html('Text'),
])
```

#### tab

> Tab field

Class: `SilverStripe\Forms\Tab`

```php
$fielder->tab($name, $titleOrField = null, $fields = null)
// ..
$fielder->tab('Primary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d'))
```

#### tabSet

> Tab set field

Class: `SilverStripe\Forms\TabSet`

```php
$fielder->tabSet($name, $titleOrTab = null, $tabs = null)
// ..
$fielder->tabSet('MyTabSetName',
    $fielder->tab('Primary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
    $fielder->tab('Secondary tab', $fielder->header('Header'), $fielder->literal('Literal', '<b>Banshee</b>')),
)
```

#### toggleComposite

> Toggle composite field

Class: `SilverStripe\Forms\ToggleCompositeField`

```php
$fielder->toggleComposite($name, $title, $children)
// ..
$fielder->toggleComposite('MyToggle', 'Toggle', [
    $fielder->string('Title'),
    $fielder->text('Text')
])
```

### ✳️ File fields

#### upload

> Upload field

Class: `SilverStripe\AssetAdmin\Forms\UploadField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
$fielder->upload($name, $title = null, SS_List $items = null)
```

#### file

> File field

Class: `SilverStripe\Forms\FileField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
$fielder->file($name, $title = null, $value = null)
```

### ✳️ Relationship fields

#### checkboxSet

> Checkbox set field

Class: `SilverStripe\Forms\CheckboxSetField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$fielder->checkboxSet($name, $title = null, $source = [], $value = null)
// ..
$fielder->checkboxSet('List', 'List', [1 => 'Set 1', 2 => 'Set 2']),
// $source = FooBar::get()->map()
```

#### dropdownTree

> Dropdown tree field (!SiteTree)

Class: `SilverStripe\Forms\TreeDropdownField`

Suitable relationship: `has_one`

```php
$fielder->dropdownTree($name, $title = null, $sourceObject = null, $keyField = 'ID', $labelField = 'TreeTitle', $showSearch = true)
// ..
$fielder->dropdownTree('Page')
```

#### treeMultiSelect

> Tree Multiselect field, only for `SilverStripe\Security\Group`

Class: `SilverStripe\Forms\TreeMultiselectField`

Suitable relationship: `has_many` `many_many`

```php
$fielder->treeMultiSelect($name, $title = null, $sourceObject = Group::class, $keyField = "ID", $labelField = "Title")
```

#### grid

> Grid field

Class: `SilverStripe\Forms\GridField\GridField` `Goldfinch\Fielder\Grid`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$fielder->grid($name, $title = null, SS_List $dataList = null, GridFieldConfig $config = null)
// ..
$fielder->grid('Services', 'Services')->build()
// ..
$fielder->grid('Services', 'Services', $this->Services())->build()
// ..
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
    ])->build()
```

* might require additional packages

#### listbox

> Listbox field

Class: `SilverStripe\Forms\ListboxField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$fielder->listbox($name, $title = null, $source = [], $value = null, $size = null)
```

### ✳️ Utility fields

#### header

> Header field

Class: `use SilverStripe\Forms\HeaderField;`

```php
$fielder->header($name, $title = null, $headingLevel = 2)
```

#### hidden

> Hidden field

Class: `SilverStripe\Forms\HiddenField`

Suitable DB Type: `*`

```php
$fielder->hidden($name, $title = null, $value = null)
```

#### label

> Label field

Class: `SilverStripe\Forms\LabelField`

```php
$fielder->label($name, $title = null)
```

#### literal

> Literal field

Class: `SilverStripe\Forms\LiteralField`

```php
$fielder->literal($name, $content)
```

#### nullable

> Nullable field

Class: `SilverStripe\Forms\NullableField`

Suitable DB Type: `*`

```php
$fielder->nullable(FormField $valueField, $isNullLabel = null)
// ..
$fielder->nullable($fielder->string('Text'))
```

#### decimal

> Decimal (DB) field

Class: `SilverStripe\ORM\FieldType\DBDecimal`

Suitable DB Type: `Decimal`

```php
$fielder->decimal($name, $title = null, $wholeSize = 9, $decimalSize = 2, $defaultValue = 0)
```

#### double

> Double (DB) field

Class: `SilverStripe\ORM\FieldType\DBDouble`

Suitable DB Type: `Double`

```php
$fielder->double($name, $title = null, $defaultVal = 0)
```

#### float

> Float (DB) field

Class: `SilverStripe\ORM\FieldType\DBFloat`

Suitable DB Type: `Float`

```php
$fielder->float($name, $title = null, $defaultVal = 0)
```

#### year

> Year (DB) field

Class: `SilverStripe\ORM\FieldType\DBYear`

Suitable DB Type: `Year`

```php
$fielder->year($name, $title = null, $options = [])
```

#### percentage

> Percentage (DB) field

Class: `SilverStripe\ORM\FieldType\DBPercentage`

Suitable DB Type: `Percentage | Percentage(6)`

```php
$fielder->percentage($name, $title = null, $precision = 4)
```

#### integer

> Integer (DB) field

Class: `SilverStripe\ORM\FieldType\DBInt`

Suitable DB Type: `Int`

```php
$fielder->int($name, $title = null, $defaultVal = 0)
```

#### big integer

> Big Integer (DB) field

Class: `SilverStripe\ORM\FieldType\DBBigInt`

Suitable DB Type: `BigInt`

```php
$fielder->bigInt($name, $title = null, $defaultVal = 0)
```

#### locale

> Locale (DB) field

Class: `SilverStripe\ORM\FieldType\DBLocale`

Suitable DB Type: `Locale`

```php
$fielder->locale($name, $title = null, $size = 16)
```

#### enum

> Enum (DB) field

Class: `SilverStripe\ORM\FieldType\DBEnum`

Suitable DB Type: `Enum("Apple,Orange,Kiwi", "Kiwi")`

```php
$fielder->enum($name)
```

#### siteTreeURLSegment

> SiteTreeURLSegment field

Class: `SilverStripe\CMS\Forms\SiteTreeURLSegmentField`

Suitable DB Type: `*`

```php
$fielder->siteTreeURLSegment($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### htmlReadonly

> HTMLReadonly field

Class: `SilverStripe\Forms\HTMLReadonlyField`

Suitable DB Type: `*`

```php
$fielder->htmlReadonly($name, $title = null, $value = null)
```

### ✳️ External fields

#### objectLink

```bash
composer require silvershop/silverstripe-hasonefield dev-main
```

> HasOneButton field

Class: `SilverShop\HasOneField\HasOneButtonField`

Suitable relationship: `has_one` `belongs_to`

```php
$fielder->objectLink($relationName, $fieldName = null, $title = null, GridFieldConfig $customConfig = null, $useAutocompleter = true)
```

#### object

```bash
composer require gorriecoe/silverstripe-linkfield ^1.1
```

> HasOneLink field

Class: `gorriecoe\LinkField\Forms\HasOneLinkField`

Suitable relationship: `has_one` `belongs_to`

```php
$fielder->object($relationName, $title = null, $linkConfig = [], $useAutocompleter = false)
```

#### multiSelect

```bash
composer require kinglozzer/multiselectfield ^2.0
```

> MultiSelect field

Class: `Kinglozzer\MultiSelectField\Forms\MultiSelectField`

Suitable relationship: `many_many` `belongs_many_many`

```php
$fielder->multiSelect($name, $title = null, $sort = false, $source = null, $titleField = 'Title')
// ..
$fielder->multiSelect('Services'),
$fielder->multiSelect('Services', 'Services', 'SortExtra'),
```

#### media

```bash
composer require goldfinch/image-editor
```

> EditableUpload field

Class: `Goldfinch\ImageEditor\Forms\EditableUploadField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
...$fielder->media($name, $title = null)
```

#### mediaSortable

```bash
composer require goldfinch/image-editor
```

> EditableSortableUpload field

Class: `Goldfinch\ImageEditor\Forms\EditableSortableUploadField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
...$fielder->mediaSortable($name, $title = null)
```

#### color

```bash
composer require heyday/silverstripe-colorpalette ^2.1
```

> ColorPalette field

Class: `Heyday\ColorPalette\Fields\ColorPaletteField`

Suitable DB Type: `*`

```php
$fielder->color($name, $title = null, $source = [], $value = null)
// ..
$fielder->color('Color', 'Color', ['yellow' => '#fee12f', 'pink' => '#eb83ad', 'green' => '#70cd77'])
```

#### colorPicker

```bash
composer require ryanpotter/silverstripe-color-field ^1.0
```

> Color field

Class: `RyanPotter\SilverStripeColorField\Forms\ColorField`

Suitable DB Type: `*`

```php
$fielder->colorPicker($name, $title = null, $value = '', $form = null)
```

Additional requirements:

```yml
# yml config (example)
RyanPotter\SilverStripeColorField\Forms\ColorField:
  colors:
    - '#1976D2'
    - '#2196F3'
```

#### colorGroup

```bash
composer require heyday/silverstripe-colorpalette ^2.1
```

> GroupedColorPalette field

Class: `Heyday\ColorPalette\Fields\GroupedColorPaletteField`

Suitable DB Type: `*`

```php
$fielder->colorGroup($name, $title = null, $source = [], $value = null)
// ..
$fielder->colorGroup('Color', 'Color', ['Primary' => ['yellow' => '#fee12f', 'pink' => '#eb83ad'], 'Secondary' => ['green' => '#70cd77']])
```

#### json

```bash
composer require goldfinch/json-editor
```

> JSONEditor field

Class: `Goldfinch\JSONEditor\Forms\JSONEditorField`

Suitable DB Type: `JSONText` `Goldfinch\JSONEditor\ORM\FieldType\DBJSONText::class`

```php
$fielder->json($name, $title = null, $options = [], $value = '{}', $form = null, $schema = '{}')
```

Additional requirements:

1) create schema fiel: ```app/_schema/Page-Parameters.json``` (app/_schema/{parent-classname}-{field-mame}.json)

2) example schema file:
```json
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
```
3) example field
```php
$fielder->json('Parameters')
```

#### video

```bash
composer require goldfinch/video-field
```

> Video field

Class: `Goldfinch\VideoField\Forms\VideoField`

Suitable DB Type: `Video`

```php
$fielder->video($name, $title = null, $value = '', $static = false)
```

See: [github.com/goldfinch/video-field](https://github.com/goldfinch/video-field)

#### place

```bash
composer require goldfinch/google-fields
```

> Place field

Class: `Goldfinch\GoogleFields\Forms\PlaceField`

Suitable DB Type: `Place`

```php
$fielder->place($name, $title = null, $value = '')
```

Additional requirements:

.env

```
APP_GOOGLE_MAPS_KEY=''
```

See: [github.com/goldfinch/google-fields](https://github.com/goldfinch/google-fields)

#### map

```bash
composer require goldfinch/google-fields
```

> Map field

Class: `Goldfinch\GoogleFields\Forms\MapField`

Suitable DB Type: `Map`

```php
$fielder->map($name, $title = null, $value = '')
```

Additional requirements:

.env

```
APP_GOOGLE_MAPS_KEY=''
```

See: [github.com/goldfinch/google-fields](https://github.com/goldfinch/google-fields)

#### link

```bash
composer require gorriecoe/silverstripe-linkfield ^1.1
```

> Link field

Class: `gorriecoe\LinkField\LinkField`

Suitable relationship: `has_one`
Relationship type: `gorriecoe\Link\Models\Link::class`

```php
$fielder->link($name, $title = null, $linkConfig = [])
// ..
$fielder->link('ALink', 'Link')
```

#### inlineLink

```bash
composer require maxime-rainville/anyfield ^0.0.0
```

> Checkbox field

Class: `SilverStripe\AnyField\Form\AnyField`

Suitable relationship: `has_one`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

Template: `vendor/silverstripe/linkfield/templates/SilverStripe/LinkField/Models/Link.ss`

```php
$fielder->inlineLink($name, $title = null, $value = null)
```

#### inlineLinks

```bash
composer require maxime-rainville/anyfield ^0.0.0
```

> ManyAny field

Class: `SilverStripe\AnyField\Form\ManyAnyField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

```php
$fielder->inlineLinks($name, $title = null, SS_List $dataList = null)
```

Additional requirements:

Required $has_one on SilverStripe\LinkField\Models\Link

eg:
```php
private static $has_one = [
    'Page' => \Page::class,
];
```

#### linkSS

```bash
composer require silverstripe/linkfield ^3
```

> Link field

Class: `SilverStripe\LinkField\Form\LinkField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

```php
$fielder->linkSS($name, $title = null, $value = null)
```

#### code

```bash
composer require kevingroeger/codeeditorfield ^1.2
```

> CodeEditor field

Class: `KevinGroeger\CodeEditorField\Forms\CodeEditorField`

Suitable DB Type: `*`

```php
$fielder->code($name, $title = null, $value = null, $mode = 'ace/mode/html', $theme = 'ace/theme/github')
```

#### tag

```bash
composer require silverstripe/tagfield ^3.0
```

> Tag field

Class: `SilverStripe\TagField\TagField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$fielder->tag($name, $title = null, $source = [], $value = null, $titleField = 'Title')
```

#### points

```bash
composer require goldfinch/silverstripe-image-points
```

> Point field

Class: `LittleGiant\SilverStripeImagePoints\DataObjects\Point`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$fielder->points($name, $title = null, $source = [], $gridconfig = null)
```

Additional requirements:

1) model
```php
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
```
2) yml config
```yml
LittleGiant\SilverStripeImagePoints\DataObjects\Point:
  image_width: 1918
  image_height: 822
```
3) example field
```php
$fielder->points('ImagePoints')
```

#### wrapper

```bash
composer require unclecheese/display-logic
```

> Wrapper field

Class: `UncleCheese\DisplayLogic\Forms\Wrapper`

```php
$fielder->wrapper($children)
```

#### autocomplete

```bash
composer require tractorcow/silverstripe-autocomplete ^5.0
```

> Autocomplete field

Class: `TractorCow\AutoComplete\AutoCompleteField`

Suitable DB Type: `*`

```php
$fielder->autocomplete($name, $title = null, $value = '', $sourceClass = null, $sourceFields = null)
// ..
$fielder->autocomplete('Page', 'Page', '', Page::class, 'Title')
```

#### stringTag

```bash
composer require silverstripe/tagfield ^3.0
```

> StringTag field

Class: `SilverStripe\TagField\StringTagField`

Suitable DB Type: `*`

```php
$fielder->stringTag($name, $title = null, $source = [], $value = null)
// ..
$fielder->stringTag('Varchar', 'Varchar', MyDataObject::get())
```

#### imageCoords

```bash
composer require goldfinch/image-editor
```

> ImageCoords field

Class: `Goldfinch\ImageEditor\Forms\ImageCoordsField`

Suitable relationship: `has_one`

```php
$fielder->imageCoords($name, $title, $onlyCanvas = false, $cssGrid = false, $image = null, $XFieldName = null, $YFieldName = null, $xySumFieldName = null, $width = null, $height = null)
// ..
$fielder->imageCoords('Image', 'Focus Point', true)
$fielder->imageCoords('Image', 'Focus Point')
```

#### encrypt

```bash
composer require lekoala/silverstripe-encrypt dev-master
```

> Encrypt field

Class: `LeKoala\Encrypt\EncryptHelper`

Suitable DB Type:
```
'MyText' => LeKoala\Encrypt\EncryptedDBText::class,
'MyHTMLText' => LeKoala\Encrypt\EncryptedDBHTMLText::class,
'MyVarchar' => LeKoala\Encrypt\EncryptedDBVarchar::class,
'MyNumber' => LeKoala\Encrypt\EncryptedNumberField::class,
'MyIndexedVarchar' => LeKoala\Encrypt\EncryptedDBField::class,
```

```php
$fielder->encrypt($name)
```

#### country

```bash
composer require dynamic/silverstripe-country-dropdown-field ^2.0
```

> CountryDropdown field

Class: `Dynamic\CountryDropdownField\Fields\CountryDropdownField`

Suitable DB Type: `*`

```php
$fielder->country($name, $title = null, $source = [], $value = '', $form = null)
```

#### icon

```bash
composer require goldfinch/icon-field
```

> Icon field

Class: `Goldfinch\IconField\Forms\IconField`

Suitable DB Type: `Icon` `Goldfinch\IconField\ORM\FieldType\DBIcon::class`

```php
$fielder->icon($set, $name, $title = null, $value = '')
```

Additional requirements:

Set .yml config
[github.com/goldfinch/icon-field](https://github.com/goldfinch/icon-field)

#### phone

```bash
composer require innoweb/silverstripe-international-phone-number-field dev-master
```

> Phone (DB) field

Class: `Innoweb\InternationalPhoneNumberField\ORM\DBPhone`

Suitable DB Type: `Phone`

```php
$fielder->phone($name, $title = null, $options = [])
```

Template output
```html
$Phone.International
$Phone.National
$Phone.E164
$Phone.RFC3966
$Phone.URL
```

```html
<% if Phone %>
<% with Phone %>
<a href="$URL" title="$International">$National</a>
<% end_with %>
<% end_if %>
```

#### mediaSelect

```bash
composer require goldfinch/silverstripe-imageselection-field
```

> Phone (DB) field

Class: `NSWDPC\Forms\ImageSelectionField\ImageSelectionField`

Suitable relationship:
```php
'Image' => Image::class,
'Images' => Image::class,
```

```php
$fielder->mediaSelect($name, $relationList, $title = null)
// ..
$fielder->mediaSelect('Image', 'Images')
```

Template output
```html
$Phone.International
$Phone.National
$Phone.E164
$Phone.RFC3966
$Phone.URL
```

## License

The MIT License (MIT)
