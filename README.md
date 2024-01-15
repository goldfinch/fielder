
# 🦅 Harvest Fields manager for Silverstripe

[![Silverstripe Version](https://img.shields.io/badge/Silverstripe-5.1-005ae1.svg?labelColor=white&logoColor=ffffff&logo=data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMDEuMDkxIDU4LjU1NSIgZmlsbD0iIzAwNWFlMSIgeG1sbnM6dj0iaHR0cHM6Ly92ZWN0YS5pby9uYW5vIj48cGF0aCBkPSJNNTAuMDE1IDUuODU4bC0yMS4yODMgMTQuOWE2LjUgNi41IDAgMCAwIDcuNDQ4IDEwLjY1NGwyMS4yODMtMTQuOWM4LjgxMy02LjE3IDIwLjk2LTQuMDI4IDI3LjEzIDQuNzg2czQuMDI4IDIwLjk2LTQuNzg1IDI3LjEzbC02LjY5MSA0LjY3NmM1LjU0MiA5LjQxOCAxOC4wNzggNS40NTUgMjMuNzczLTQuNjU0QTMyLjQ3IDMyLjQ3IDAgMCAwIDUwLjAxNSA1Ljg2MnptMS4wNTggNDYuODI3bDIxLjI4NC0xNC45YTYuNSA2LjUgMCAxIDAtNy40NDktMTAuNjUzTDQzLjYyMyA0Mi4wMjhjLTguODEzIDYuMTctMjAuOTU5IDQuMDI5LTI3LjEyOS00Ljc4NHMtNC4wMjktMjAuOTU5IDQuNzg0LTI3LjEyOWw2LjY5MS00LjY3NkMyMi40My0zLjk3NiA5Ljg5NC0uMDEzIDQuMTk4IDEwLjA5NmEzMi40NyAzMi40NyAwIDAgMCA0Ni44NzUgNDIuNTkyeiIvPjwvc3ZnPg==)](https://packagist.org/packages/spatie/schema-org)
[![Package Version](https://img.shields.io/packagist/v/goldfinch/harvest.svg?labelColor=333&color=F8C630&label=Version)](https://packagist.org/packages/spatie/schema-org)
[![Total Downloads](https://img.shields.io/packagist/dt/goldfinch/harvest.svg?labelColor=333&color=F8C630&label=Downloads)](https://packagist.org/packages/spatie/schema-org)
[![License](https://img.shields.io/packagist/l/goldfinch/harvest.svg?labelColor=333&color=F8C630&label=License)](https://packagist.org/packages/spatie/schema-org) 

Harvest 🚜 is fields manager and organizer that helps to simplify fields declaration and makes it easy to manage all in one place, keeping it clean with less code.

## Install

```
composer require goldfinch/harvest
```

## Usage

Add `harvest` method to your DataObject/Page

```php
use SilverStripe\ORM\DataObject;
use Goldfinch\Harvest\Traits\HarvestTrait;

MyAwesomeModel extends DataObject
{
    use HarvestTrait;

    public function harvest(Harvest $harvest)
    {
        $harvest->remove('Content');

        $harvest->require([
            'FirstName',
            'About',
        ]);

        $harvest->fields([
            'Root.Main' => [
                $harvest->string('FirstName', 'First name'),
                $harvest->text('About'),
                $harvest->html('Content'),
            ],
            'Root.Demo' => [
                $harvest->string('Headline'),
            ],
        ]);
    }
}
```

Add `harvestSettings` method to your `SiteTree` page to manage settings fields (insetead of `getSettingsFields`)

```php
use SilverStripe\CMS\Model\SiteTree;
use Goldfinch\Harvest\Traits\HarvestTrait;

MyAwesomePage extends SiteTree
{
    use HarvestTrait;

    public function harvestSettings(Harvest $harvest)
    {
        $harvest->remove('ShowInMenus');
    }
}
```

If for some reason you need to keep `getCMSFields` or `getSettingsFields` but want to use `harvest`, you can do that. Just don't use trait and declare both methods as shown below.

```php
use SilverStripe\CMS\Model\SiteTree;

MyAwesomePage extends SiteTree
{
    public function harvest(Harvest $harvest)
    {
        $harvest->remove('Content');

        $harvest->require([
            'FirstName',
            'About',
        ]);

        $harvest->fields([
            'Root.Main' => [
                $harvest->string('FirstName', 'First name'),
                $harvest->text('About'),
                $harvest->html('Content'),
            ],
            'Root.Demo' => [
                $harvest->string('Headline'),
            ],
        ]);
    }

    public function harvestSettings(Harvest $harvest)
    {
        $harvest->remove('ShowInMenus');
    }

    public function getCMSFields()
    {
        $fields = parent::getCMSFields();

        // .. $fields->addFieldsToTab()

        return $this->harvestFields($fields)->getFields();
    }

    public function getSettingsFields()
    {
        $fields = parent::getSettingsFields();

        // .. $fields->removeByName()

        return $this->harvestSettingsFields($fields)->getFields();
    }
}
```

#### Available extends that can be triggered via extensions

```php
use Goldfinch\Harvest\Harvest;

public function updateHarvest(Harvest $harvest)
{
    // ..
}
public function updateHarvestSettings(Harvest $harvest)
{
    // ..
}
public function updateHarvestCompositeValidator(Harvest $harvest)
{
    // ..
}
public function updateHarvestValidate(Harvest $harvest)
{
    // ..
}
```

## List of available methods

> declare fields in tabs
```php
$harvest->fields([
  'Root.Main' => [
    $harvest->string('Title'),
  ],
])
```

> required field

```php
$harvest->require('Title')
$harvest->require(['Title', 'Content']);
```

> remove specific fields
```php
$harvest->remove('Title');
```

> remove all fields 
```php
$harvest->removeAll();
```

> remove all fields within the class (ignores fields that were added through inherited or extended classes)
```php
$harvest->removeAllCurrent();

> add custom error
```php
$harvest->addError('Error message');
```

## List of available fields

### ✳️ General fields

#### checkbox

> Checkbox field

Class: `SilverStripe\Forms\CheckboxField`

Suitable DB Type: `Boolean`

```php
$harvest->checkbox($name, $title = null, $value = null)
```

#### dropdown

> Dropdown field

Class: `SilverStripe\Forms\DropdownField`

Suitable DB Type: `*`

```php
$harvest->dropdown($name, $title = null, $source = [], $value = null)
```

#### readonly

> Readonly field

Class: `SilverStripe\Forms\ReadonlyField`

Suitable DB Type: `*`

```php
$harvest->readonly($name, $title = null, $value = null)
```

#### text

> Textarea field

Class: `SilverStripe\Forms\TextareaField`

Suitable DB Type: `*`

```php
$harvest->text($name, $title = null, $value = null)
```

#### string

> Text field

Class: `SilverStripe\Forms\TextField`

Suitable DB Type: `Varchar`

```php
$harvest->string($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### password

> Password field

Class: `SilverStripe\Forms\PasswordField`

Suitable DB Type: `Varchar`

```php
$harvest->password($name, $title = null, $value = '')
```

#### action

> Action field

Class: `SilverStripe\Forms\FormAction`

```php
$harvest->action($action, $title = '', $form = null)
```

#### passwordConfirmed

> Password confirm field

Class: `SilverStripe\Forms\ConfirmedPasswordField`

Suitable DB Type: `*`

```php
$harvest->passwordConfirmed($name, $title = null, $value = '', $form = null, $showOnClick = false, $titleConfirmField = null)
```

#### currency

> Currency field

Class: `SilverStripe\Forms\CurrencyField`

Suitable DB Type: `Currency`

```php
$harvest->currency($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### date

> Date field

Class: `SilverStripe\Forms\DateField`

Suitable DB Type: `Date`

```php
$harvest->date($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### datetime

> Datetime field

Class: `SilverStripe\Forms\DatetimeField`

Suitable DB Type: `Datetime`

```php
$harvest->datetime($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### email

> Email field

Class: `SilverStripe\Forms\EmailField`

Suitable DB Type: `Varchar`

```php
$harvest->email($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### groupedDropdown

> Grouped dropdown field

Class: `SilverStripe\Forms\GroupedDropdownField`

Suitable DB Type: `*`

```php
$harvest-> groupedDropdown($name, $title = null, $source = [], $value = null)
// ..
$harvest->groupedDropdown('Name', 'Title', [
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
$harvest->html($name, $title = null, $value = '', $config = null)
```

#### money

> Money field

Class: `SilverStripe\Forms\MoneyField`

Suitable DB Type: `Money`

```php
$harvest->money($name, $title = null, $value = '')
```

#### numeric

> Numeric field

Class: `SilverStripe\Forms\NumericField`

Suitable DB Type: `Decimal` `Float` `Int`

```php
$harvest->numeric($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### radio

> Radio field

Class: `SilverStripe\Forms\OptionsetField`

Suitable DB Type: `*`

```php
$harvest->radio($name, $title = null, $source = [], $value = null)
// ..
$harvest->radio('Name', 'Title', [1 => 'Option 1', 2 => 'Option 2'])
// $source = FooBar::get()->map()
```

#### selectionGroup

> Selection group field

Class: `SilverStripe\Forms\SelectionGroup`

Suitable DB Type: `*`

```php
$harvest->selectionGroup($name, $items, $value = null)
// ..
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
])
```

#### time

> Time field

Class: `SilverStripe\Forms\TimeField`

Suitable DB Type: `Time`

```php
$harvest->time($name, $title = null, $value = '', $maxLength = null, $form = null)
```

### ✳️ Structure fields

#### composite

> Composite field

Class: `SilverStripe\Forms\CompositeField`

```php
$harvest->composite($children = null)
// ..
$harvest->composite([
    $harvest->string('Title'),
    $harvest->html('Text'),
])
```

#### group

> Group field

Class: `SilverStripe\Forms\FieldGroup`

```php
$harvest->group($titleOrField = null, $otherFields = null)
// ..
$harvest->group('Group Title', [
    $harvest->string('Title'),
    $harvest->html('Text'),
])
```

#### list

> List field. FYI: $fields is FieldList already. Using this field we store new FieldList in FieldList

Class: `SilverStripe\Forms\FieldList`

```php
$harvest->list($items = [])
// ..
...$harvest->list([
    $harvest->string('Title'),
    $harvest->html('Text'),
])
```

#### tab

> Tab field

Class: `SilverStripe\Forms\Tab`

```php
$harvest->tab($name, $titleOrField = null, $fields = null)
// ..
$harvest->tab('Primary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d'))
```

#### tabSet

> Tab set field

Class: `SilverStripe\Forms\TabSet`

```php
$harvest->tabSet($name, $titleOrTab = null, $tabs = null)
// ..
$harvest->tabSet('MyTabSetName',
    $harvest->tab('Primary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Br</b>eaking <b>Ba</b>d')),
    $harvest->tab('Secondary tab', $harvest->header('Header'), $harvest->literal('Literal', '<b>Banshee</b>')),
)
```

#### toggleComposite

> Toggle composite field

Class: `SilverStripe\Forms\ToggleCompositeField`

```php
$harvest->toggleComposite($name, $title, $children)
// ..
$harvest->toggleComposite('MyToggle', 'Toggle', [
    $harvest->string('Title'),
    $harvest->text('Text')
])
```

### ✳️ File fields

#### upload

> Upload field

Class: `SilverStripe\AssetAdmin\Forms\UploadField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
$harvest->upload($name, $title = null, SS_List $items = null)
```

#### file

> File field

Class: `SilverStripe\Forms\FileField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
$harvest->file($name, $title = null, $value = null)
```

### ✳️ Relationship fields

#### checkboxSet

> Checkbox set field

Class: `SilverStripe\Forms\CheckboxSetField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->checkboxSet($name, $title = null, $source = [], $value = null)
// ..
$harvest->checkboxSet('List', 'List', [1 => 'Set 1', 2 => 'Set 2']),
// $source = FooBar::get()->map()
```

#### dropdownTree

> Dropdown tree field (!SiteTree)

Class: `SilverStripe\Forms\TreeDropdownField`

Suitable relationship: `has_one`

```php
$harvest->dropdownTree($name, $title = null, $sourceObject = null, $keyField = 'ID', $labelField = 'TreeTitle', $showSearch = true)
// ..
$harvest->dropdownTree('Page')
```

#### treeMultiSelect

> Tree Multiselect field, only for `SilverStripe\Security\Group`

Class: `SilverStripe\Forms\TreeMultiselectField`

Suitable relationship: `has_many` `many_many`

```php
$harvest->treeMultiSelect($name, $title = null, $sourceObject = Group::class, $keyField = "ID", $labelField = "Title")
```

#### grid

> Grid field

Class: `SilverStripe\Forms\GridField\GridField` `Goldfinch\Harvest\Grid`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->grid($name, $title = null, SS_List $dataList = null, GridFieldConfig $config = null)
// ..
$harvest->grid('Services', 'Services')->build()
// ..
$harvest->grid('Services', 'Services', $this->Services())->build()
// ..
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
    ])->build()
```

* might require additional packages

#### listbox

> Listbox field

Class: `SilverStripe\Forms\ListboxField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->listbox($name, $title = null, $source = [], $value = null, $size = null)
```

### ✳️ Utility fields

#### header

> Header field

Class: `use SilverStripe\Forms\HeaderField;`

```php
$harvest->header($name, $title = null, $headingLevel = 2)
```

#### hidden

> Hidden field

Class: `SilverStripe\Forms\HiddenField`

Suitable DB Type: `*`

```php
$harvest->hidden($name, $title = null, $value = null)
```

#### label

> Label field

Class: `SilverStripe\Forms\LabelField`

```php
$harvest->label($name, $title = null)
```

#### literal

> Literal field

Class: `SilverStripe\Forms\LiteralField`

```php
$harvest->literal($name, $content)
```

#### nullable

> Nullable field

Class: `SilverStripe\Forms\NullableField`

Suitable DB Type: `*`

```php
$harvest->nullable(FormField $valueField, $isNullLabel = null)
// ..
$harvest->nullable($harvest->string('Text'))
```

#### decimal

> Decimal (DB) field

Class: `SilverStripe\ORM\FieldType\DBDecimal`

Suitable DB Type: `Decimal`

```php
$harvest->decimal($name, $title = null, $wholeSize = 9, $decimalSize = 2, $defaultValue = 0)
```

#### double

> Double (DB) field

Class: `SilverStripe\ORM\FieldType\DBDouble`

Suitable DB Type: `Double`

```php
$harvest->double($name, $title = null, $defaultVal = 0)
```

#### float

> Float (DB) field

Class: `SilverStripe\ORM\FieldType\DBFloat`

Suitable DB Type: `Float`

```php
$harvest->float($name, $title = null, $defaultVal = 0)
```

#### year

> Year (DB) field

Class: `SilverStripe\ORM\FieldType\DBYear`

Suitable DB Type: `Year`

```php
$harvest->year($name, $title = null, $options = [])
```

#### percentage

> Percentage (DB) field

Class: `SilverStripe\ORM\FieldType\DBPercentage`

Suitable DB Type: `Percentage | Percentage(6)`

```php
$harvest->percentage($name, $title = null, $precision = 4)
```

#### integer

> Integer (DB) field

Class: `SilverStripe\ORM\FieldType\DBInt`

Suitable DB Type: `Int`

```php
$harvest->int($name, $title = null, $defaultVal = 0)
```

#### big integer

> Big Integer (DB) field

Class: `SilverStripe\ORM\FieldType\DBBigInt`

Suitable DB Type: `BigInt`

```php
$harvest->bigInt($name, $title = null, $defaultVal = 0)
```

#### locale

> Locale (DB) field

Class: `SilverStripe\ORM\FieldType\DBLocale`

Suitable DB Type: `Locale`

```php
$harvest->locale($name, $title = null, $size = 16)
```

#### enum

> Enum (DB) field

Class: `SilverStripe\ORM\FieldType\DBEnum`

Suitable DB Type: `Enum("Apple,Orange,Kiwi", "Kiwi")`

```php
$harvest->enum($name)
```

#### siteTreeURLSegment

> SiteTreeURLSegment field

Class: `SilverStripe\CMS\Forms\SiteTreeURLSegmentField`

Suitable DB Type: `*`

```php
$harvest->siteTreeURLSegment($name, $title = null, $value = '', $maxLength = null, $form = null)
```

#### htmlReadonly

> HTMLReadonly field

Class: `SilverStripe\Forms\HTMLReadonlyField`

Suitable DB Type: `*`

```php
$harvest->htmlReadonly($name, $title = null, $value = null)
```

### ✳️ External fields

#### objectLink

```
composer require silvershop/silverstripe-hasonefield dev-main
```

> HasOneButton field

Class: `SilverShop\HasOneField\HasOneButtonField`

Suitable relationship: `has_one` `belongs_to`

```php
$harvest->objectLink($relationName, $fieldName = null, $title = null, GridFieldConfig $customConfig = null, $useAutocompleter = true)
```

#### object

```
composer require gorriecoe/silverstripe-linkfield ^1.1
```

> HasOneLink field

Class: `gorriecoe\LinkField\Forms\HasOneLinkField`

Suitable relationship: `has_one` `belongs_to`

```php
$harvest->object($relationName, $title = null, $linkConfig = [], $useAutocompleter = false)
```

#### multiSelect

```
composer require kinglozzer/multiselectfield ^2.0
```

> MultiSelect field

Class: `Kinglozzer\MultiSelectField\Forms\MultiSelectField`

Suitable relationship: `many_many` `belongs_many_many`

```php
$harvest->multiSelect($name, $title = null, $sort = false, $source = null, $titleField = 'Title')
// ..
$harvest->multiSelect('Services'),
$harvest->multiSelect('Services', 'Services', 'SortExtra'),
```

#### media

```
composer require goldfinch/image-editor
```

> EditableUpload field

Class: `Goldfinch\ImageEditor\Forms\EditableUploadField`

Suitable relationship: `has_one` `has_many` `many_many` `belongs_many_many`

```php
...$harvest->media($name, $title = null)
```

#### mediaSortable

```
composer require goldfinch/image-editor
```

> EditableSortableUpload field

Class: `Goldfinch\ImageEditor\Forms\EditableSortableUploadField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
...$harvest->mediaSortable($name, $title = null)
```

#### color

```
composer require heyday/silverstripe-colorpalette ^2.1
```

> ColorPalette field

Class: `Heyday\ColorPalette\Fields\ColorPaletteField`

Suitable DB Type: `*`

```php
$harvest->color($name, $title = null, $source = [], $value = null)
// ..
$harvest->color('Color', 'Color', ['yellow' => '#fee12f', 'pink' => '#eb83ad', 'green' => '#70cd77'])
```

#### colorPicker

```
composer require ryanpotter/silverstripe-color-field ^1.0
```

> Color field

Class: `RyanPotter\SilverStripeColorField\Forms\ColorField`

Suitable DB Type: `*`

```php
$harvest->colorPicker($name, $title = null, $value = '', $form = null)
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

```
composer require heyday/silverstripe-colorpalette ^2.1
```

> GroupedColorPalette field

Class: `Heyday\ColorPalette\Fields\GroupedColorPaletteField`

Suitable DB Type: `*`

```php
$harvest->colorGroup($name, $title = null, $source = [], $value = null)
// ..
$harvest->colorGroup('Color', 'Color', ['Primary' => ['yellow' => '#fee12f', 'pink' => '#eb83ad'], 'Secondary' => ['green' => '#70cd77']])
```

#### json

```
composer require goldfinch/json-editor
```

> JSONEditor field

Class: `Goldfinch\JSONEditor\Forms\JSONEditorField`

Suitable DB Type: `JSONText` `Goldfinch\JSONEditor\ORM\FieldType\DBJSONText::class`

```php
$harvest->json($name, $title = null, $options = [], $value = '{}', $form = null, $schema = '{}')
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
$harvest->json('Parameters')
```

#### place

```
composer require goldfinch/google-fields
```

> Place field

Class: `Goldfinch\GoogleFields\Forms\PlaceField`

Suitable DB Type: `Place`

```php
$harvest->place($name, $title = null, $value = '')
```

Additional requirements:

.env

```
APP_GOOGLE_MAPS_KEY=''
```

See: [github.com/goldfinch/google-fields](https://github.com/goldfinch/google-fields)

#### map

```
composer require goldfinch/google-fields
```

> Map field

Class: `Goldfinch\GoogleFields\Forms\MapField`

Suitable DB Type: `Map`

```php
$harvest->map($name, $title = null, $value = '')
```

Additional requirements:

.env

```
APP_GOOGLE_MAPS_KEY=''
```

See: [github.com/goldfinch/google-fields](https://github.com/goldfinch/google-fields)

#### link

```
composer require gorriecoe/silverstripe-linkfield ^1.1
```

> Link field

Class: `gorriecoe\LinkField\LinkField`

Suitable relationship: `has_one`
Relationship type: `gorriecoe\Link\Models\Link::class`

```php
$harvest->link($name, $title = null, $linkConfig = [])
// ..
$harvest->link('ALink', 'Link')
```

#### inlineLink

```
composer require maxime-rainville/anyfield ^0.0.0
```

> Checkbox field

Class: `SilverStripe\AnyField\Form\AnyField`

Suitable relationship: `has_one`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

```php
$harvest->inlineLink($name, $title = null, $value = null)
```

#### inlineLinks

```
composer require maxime-rainville/anyfield ^0.0.0
```

> ManyAny field

Class: `SilverStripe\AnyField\Form\ManyAnyField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

```php
$harvest->inlineLinks($name, $title = null, SS_List $dataList = null)
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

```
composer require silverstripe/linkfield ^3
```

> Link field

Class: `SilverStripe\LinkField\Form\LinkField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`
Relationship type: `SilverStripe\LinkField\Models\Link::class`

```php
$harvest->linkSS($name, $title = null, $value = null)
```

#### code

```
composer require kevingroeger/codeeditorfield ^1.2
```

> CodeEditor field

Class: `KevinGroeger\CodeEditorField\Forms\CodeEditorField`

Suitable DB Type: `*`

```php
$harvest->code($name, $title = null, $value = null, $mode = 'ace/mode/html', $theme = 'ace/theme/github')
```

#### tag

```
composer require silverstripe/tagfield ^3.0
```

> Tag field

Class: `SilverStripe\TagField\TagField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->tag($name, $title = null, $source = [], $value = null, $titleField = 'Title')
```

#### points

```
composer require goldfinch/silverstripe-image-points
```

> Point field

Class: `LittleGiant\SilverStripeImagePoints\DataObjects\Point`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->points($name, $title = null, $source = [], $gridconfig = null)
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
$harvest->points('ImagePoints')
```

#### wrapper

```
composer require unclecheese/display-logic
```

> Wrapper field

Class: `UncleCheese\DisplayLogic\Forms\Wrapper`

```php
$harvest->wrapper($children = null)
```

#### autocomplete

```
composer require tractorcow/silverstripe-autocomplete ^5.0
```

> Autocomplete field

Class: `TractorCow\AutoComplete\AutoCompleteField`

Suitable DB Type: `*`

```php
$harvest->autocomplete($name, $title = null, $value = '', $sourceClass = null, $sourceFields = null)
// ..
$harvest->autocomplete('Page', 'Page', '', Page::class, 'Title')
```

#### stringTag

```
composer require silverstripe/tagfield ^3.0
```

> StringTag field

Class: `SilverStripe\TagField\StringTagField`

Suitable DB Type: `*`

```php
$harvest->stringTag($name, $title = null, $source = [], $value = null)
// ..
$harvest->stringTag('Varchar', 'Varchar', MyDataObject::get())
```

#### imageCoords

```
composer require goldfinch/image-editor
```

> ImageCoords field

Class: `Goldfinch\ImageEditor\Forms\ImageCoordsField`

Suitable relationship: `has_one`

```php
$harvest->imageCoords($name, $title, $onlyCanvas = false, $cssGrid = false, $image = null, $XFieldName = null, $YFieldName = null, $xySumFieldName = null, $width = null, $height = null)
// ..
$harvest->imageCoords('Image', 'Focus Point', true)
$harvest->imageCoords('Image', 'Focus Point')
```

#### encrypt

```
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
$harvest->encrypt($name)
```

#### country

```
composer require dynamic/silverstripe-country-dropdown-field ^2.0
```

> CountryDropdown field

Class: `Dynamic\CountryDropdownField\Fields\CountryDropdownField`

Suitable DB Type: `*`

```php
$harvest->country($name, $title = null, $source = [], $value = '', $form = null)
```

#### iconFile

```
composer require goldfinch/icon
```

> IconFile field

Class: `Goldfinch\Icon\Forms\IconFileField`

Suitable DB Type: `Icon` `Goldfinch\Icon\ORM\FieldType\DBIcon::class`

```php
$harvest->iconFile($name, $title = null, $sourceFolder = null)
```

Additional requirements:

Set .yml config
[github.com/goldfinch/icon](https://github.com/goldfinch/icon)

#### iconFont

```
composer require goldfinch/icon
```

> IconFont field

Class: `Goldfinch\Icon\Forms\IconFontField`

Suitable DB Type: `Icon` `Goldfinch\Icon\ORM\FieldType\DBIcon::class`

```php
$harvest->iconFont($name, $title = null)
```

Additional requirements:

Set .yml config
[github.com/goldfinch/icon](https://github.com/goldfinch/icon)

#### phone

```
composer require innoweb/silverstripe-international-phone-number-field dev-master
```

> Phone (DB) field

Class: `Innoweb\InternationalPhoneNumberField\ORM\DBPhone`

Suitable DB Type: `Phone`

```php
$harvest->phone($name, $title = null, $options = [])
```

Template output
```html
$Phone.International
$Phone.National
$Phone.E164
$Phone.RFC3966
$Phone.URL
```

#### mediaSelect

```
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
$harvest->mediaSelect($name, $relationList, $title = null)
// ..
$harvest->mediaSelect('Image', 'Images')
```

Template output
```html
$Phone.International
$Phone.National
$Phone.E164
$Phone.RFC3966
$Phone.URL
```
