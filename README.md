harvest gets involved only if harvest() or harvestSettings() presented

for getCMSFields
```
public function harvest(Harvest $harvest)
{
    $harvest->clear('Content');
    // $harvest->clearAll();
    // $harvest->addError('Error message');
    $harvest->required([
        'Color',
        'Varchar',
        'Icon',
    ]);

    return $harvest->fields([
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

    $harvest->required([
        'ShowInFooter'
    ]);

    return $harvest->getFields();
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
