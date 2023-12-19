harvest gets involved only if harvest() or harvestSettings() presented

for getCMSFields
```
public function harvest(Harvest $harvest)
{
    $harvest->clear('Content');
    // $harvest->clearAll();
    // $harvest->clearAllCurrent();
    // $harvest->addError('Error message');
    $harvest->require([
        'Color',
        'Varchar',
        'Icon',
    ]);

    $harvest->fields([
        'Root.Main' => [
            $harvest->colorPicker('Color'),
            $harvest->string('Varchar'),
        ],
        'Root.Demo' => [
            $harvest->iconFile('Icon'),
        ],
    ]);
}
```

for getSettingsFields (SiteTree only)
```
public function harvestSettings(Harvest $harvest)
{
    $harvest->clear('ShowInMenus');

    $harvest->require([
        'ShowInFooter'
    ]);

    // $harvest->getFields();
}
```

(optional) - fields could be extended by other external modules which sometimes lead to a missmatch due to sequence. To make sure harvest() and harvestSettings() receved latest $fields, apply trait to the class

```
use Goldfinch\Harvest\Traits\HarvestTrait;

class Page {
  
  use HarvestTrait;

  ...
}
```

available extends

```
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



has_one | belongs_to
```
dropdown
groupedDropdown
radio
dropdownTree
objectLink
object
autocomplete
// - selectionGroup
```

has_many | many_many | belongs_many_many
```
checkboxSet
listbox
checkboxSet
tag
```

many_many | belongs_many_many
```
multiSelect
```

links
```
link
linkSS
inlineLink
inlineLinks
```
