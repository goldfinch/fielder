
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

..

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

#### listbox

> Listbox field

Class: `SilverStripe\Forms\ListboxField`

Suitable relationship: `has_many` `many_many` `belongs_many_many`

```php
$harvest->listbox($name, $title = null, $source = [], $value = null, $size = null)
```

### ✳️ Utility fields


