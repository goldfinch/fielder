<?php

namespace Goldfinch\Harvest;

use Unisolutions\GridField\CopyButton;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridFieldFooter;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridFieldDetailForm;
use SilverStripe\Forms\GridField\GridFieldEditButton;
use SilverStripe\Forms\GridField\GridFieldLazyLoader;
use SilverStripe\Forms\GridField\GridFieldViewButton;
use SilverStripe\Forms\GridField\GridField_ActionMenu;
use SilverStripe\Forms\GridField\GridFieldDataColumns;
use SilverStripe\Forms\GridField\GridFieldPrintButton;
use Symbiote\GridFieldExtensions\GridFieldTitleHeader;
use Goldfinch\Nest\Forms\GridField\GridFieldNestConfig;
use SilverStripe\Forms\GridField\GridFieldAddNewButton;
use SilverStripe\Forms\GridField\GridFieldDeleteAction;
use SilverStripe\Forms\GridField\GridFieldExportButton;
use SilverStripe\Forms\GridField\GridFieldFilterHeader;
use SilverStripe\Forms\GridField\GridFieldImportButton;
use SilverStripe\Forms\GridField\GridFieldToolbarHeader;
use Symbiote\GridFieldExtensions\GridFieldOrderableRows;
use SilverStripe\Forms\GridField\GridFieldSortableHeader;
use Symbiote\GridFieldExtensions\GridFieldEditableColumns;
use Symbiote\GridFieldExtensions\GridFieldAddNewMultiClass;
use SilverStripe\Forms\GridField\GridFieldGroupDeleteAction;
use Symbiote\GridFieldExtensions\GridFieldAddNewInlineButton;
use Goldfinch\Helpers\Forms\GridField\GridFieldManyManyConfig;
use Symbiote\GridFieldExtensions\GridFieldConfigurablePaginator;
use Symbiote\GridFieldExtensions\GridFieldAddExistingSearchButton;
use SilverStripe\Forms\GridField\GridFieldAddExistingAutocompleter;
use Goldfinch\FocusPointExtra\Forms\GridField\GridFieldManyManyFocusConfig;
use Axllent\MetaEditor\Forms\GridField\GridFieldLevelup;

class Grid
{
    private $grid = null;

    private $fields = null;

    private $parent = null;

    private $components = [
        'add-multi' => GridFieldAddNewMultiClass::class,
        'add-existing-auto' => GridFieldAddExistingAutocompleter::class,

        'toolbar-header' => GridFieldToolbarHeader::class,
        'sortable-header' => GridFieldSortableHeader::class,
        'filter-header' => GridFieldFilterHeader::class,
        'title-header' => GridFieldTitleHeader::class,

        'detail-form' => GridFieldDetailForm::class,
        'data-columns' => GridFieldDataColumns::class,
        'paginator' => GridFieldPaginator::class,

        'action-menu' => GridField_ActionMenu::class,
        // 'group-delete' => GridFieldGroupDeleteAction::class,
        'add' => GridFieldAddNewButton::class,
        'add-existing' => GridFieldAddExistingSearchButton::class,
        // 'add-inline' => GridFieldAddNewInlineButton::class,
        'view' => GridFieldViewButton::class,
        'edit' => GridFieldEditButton::class,
        'export' => GridFieldExportButton::class,
        'print' => GridFieldPrintButton::class,
        'import' => GridFieldImportButton::class,
        'delete' => GridFieldDeleteAction::class,
        'copy' => CopyButton::class,

        'editable-columns' => GridFieldEditableColumns::class,
        'orderable-rows' => GridFieldOrderableRows::class,
        'configurable-paginator' => GridFieldConfigurablePaginator::class,
        'page-count' => GridFieldPageCount::class,
        'button-row' => GridFieldButtonRow::class,
        'footer' => GridFieldFooter::class,
        'levelup' => GridFieldLevelup::class,
        'lazy-loader' => GridFieldLazyLoader::class,
    ];

    private $configs = [
        'nest' => GridFieldNestConfig::class,
        'many-many' => GridFieldManyManyConfig::class,
        'many-many-focus' => GridFieldManyManyFocusConfig::class,

        'default' => [
            'detail-form',
            'action-menu',
            'add',
            'edit',
            'copy',
            'delete',
        ],
    ];

    public function __construct(&$fields, $parent)
    {
        $this->fields = $fields;
        $this->parent = $parent->getOwner();
    }

    public function init($name, $title, $dataList, $config)
    {
        if (!$dataList)
        {
            $relation = $this->parent->getRelationType($name);

            if (in_array($relation, ['has_many', 'many_many', 'belongs_many_many']))
            {
                $dataList = $this->parent->$name();
            }
        }

        $this->grid = GridField::create($name, $title, $dataList, $config);

        return $this;
    }

    public function build()
    {
        return $this->grid;
    }

    public function config($config)
    {
        if (isset($this->configs[$config]))
        {
            if (is_string($this->configs[$config]))
            {
                $class = $this->configs[$config];
                $this->grid->setConfig(new $class());
            }
            else if (is_array($this->configs[$config]))
            {
                $this->components($this->configs[$config]);
            }
        }
        else
        {
            $this->grid->setConfig($config);
        }

        return $this;
    }

    public function remove($components)
    {
        if (!empty($components))
        {
            foreach($components as $component => $args)
            {
                if (is_array($args))
                {
                    if (isset($this->components[$component]))
                    {
                        $this->grid->getConfig()->removeComponentsByType($this->components[$component]);
                    }
                }
                else
                {
                    if (isset($this->components[$args]))
                    {
                        $this->grid->getConfig()->removeComponentsByType($this->components[$args]);
                    }
                }
            }
        }

        return $this;
    }

    public function components($components)
    {
        if (!empty($components))
        {
            foreach($components as $component => $args)
            {
                if (is_array($args))
                {
                    if (isset($this->components[$component]))
                    {
                        $this->grid->getConfig()->addComponent(new $this->components[$component](...$args));
                    }
                }
                else
                {
                    if (isset($this->components[$args]))
                    {
                        $this->grid->getConfig()->addComponent(new $this->components[$args]());
                    }
                }
            }
        }

        return $this;
    }
}
