<?php

namespace Goldfinch\Fielder;

use League\Uri\Components\Component;
use Unisolutions\GridField\CopyButton;
use SilverStripe\Forms\GridField\GridField;
use SilverStripe\Forms\GridField\GridState;
use SilverStripe\Forms\GridField\GridFieldFooter;
use SilverStripe\Forms\GridField\GridFieldButtonRow;
use SilverStripe\Forms\GridField\GridFieldPageCount;
use SilverStripe\Forms\GridField\GridFieldPaginator;
use SilverStripe\Forms\GridField\GridState_Component;
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
use Axllent\MetaEditor\Forms\GridField\GridFieldLevelup;
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
use Goldfinch\ImageEditor\Forms\GridField\GridFieldManyManyFocusConfig;
use SilverStripe\Versioned\VersionedGridFieldState\VersionedGridFieldState;

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
            'toolbar-header',
            'button-row',
            'sortable-header',
            'filter-header',
            'data-columns',
            'page-count',
            'paginator',
        ],

        'basic' => [
            'add',
            'toolbar-header',
            'data-columns',
            'detail-form',
            'delete',
            'edit',
            'action-menu',
            'copy',
        ],

        'basic-sort' => [
            'add',
            'toolbar-header',
            'data-columns',
            'detail-form',
            'delete',
            'edit',
            'action-menu',
            'copy',
            'orderable-rows' => 'SortOrder',
        ],
    ];

    public function __construct(&$fields, $parent)
    {
        $this->fields = $fields;
        $this->parent = $parent->getOwner();
    }

    public function init($name, $title, $dataList, $config)
    {
        if (!$dataList) {
            $relation = $this->parent->getRelationType($name);

            if (
                in_array($relation, [
                    'has_many',
                    'many_many',
                    'belongs_many_many',
                ])
            ) {
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
        // // remove existing components first
        // $cfg = $this->grid->getConfig();

        // foreach ($cfg->getComponents() as $c) {

        //     if (get_class($c) != VersionedGridFieldState::class && get_class($c) != GridState_Component::class) {
        //         $cfg->removeComponentsByType($c);
        //     }
        // }

        // apply config components
        if (is_string($config) && isset($this->configs[$config])) {
            if (is_string($this->configs[$config])) {
                $class = $this->configs[$config];
                $this->grid->setConfig(new $class());
            } elseif (is_array($this->configs[$config])) {
                $this->components($this->configs[$config]);
            }
        } else {
            $this->grid->setConfig($config);
        }

        return $this;
    }

    public function remove($components)
    {
        if (!empty($components)) {
            if (!is_array($components)) {
                $this->grid
                    ->getConfig()
                    ->removeComponentsByType($components);
            } else {
                foreach ($components as $component => $args) {
                    if (is_array($args)) {
                        if (isset($this->components[$component])) {
                            $this->grid
                                ->getConfig()
                                ->removeComponentsByType(
                                    $this->components[$component],
                                );
                        }
                    } else {
                        if (isset($this->components[$args])) {
                            $this->grid
                                ->getConfig()
                                ->removeComponentsByType($this->components[$args]);
                        }
                    }
                }
            }
        }

        return $this;
    }

    public function getByType($type)
    {
        return $this->grid->getConfig()->getComponentByType($type);
    }

    public function components($components)
    {
        if (!empty($components)) {
            foreach ($components as $component => $args) {
                if (is_array($args)) {
                    if (isset($this->components[$component])) {
                        $this->remove($this->components[$component]);

                        $this->grid
                            ->getConfig()
                            ->addComponent(
                                new ($this->components[$component])(...$args),
                            );
                    }
                } else {
                    if (is_numeric($component) && isset($this->components[$args])) {
                        $this->remove($this->components[$args]);

                        $this->grid
                            ->getConfig()
                            ->addComponent(new ($this->components[$args])());
                    } else if (isset($this->components[$component])) {
                        $this->remove($this->components[$component]);

                        $this->grid
                            ->getConfig()
                            ->addComponent(new ($this->components[$component])($args));
                    }
                }
            }
        }

        return $this;
    }
}
