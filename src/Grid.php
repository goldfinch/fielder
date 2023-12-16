<?php

namespace Goldfinch\Harvest;

use SilverStripe\Forms\GridField\GridField;

class Grid
{
    private $grid = null;

    private $fields = null;

    private $parent = null;

    public function __construct(&$fields, $parent)
    {
        $this->fields = $fields;
        $this->parent = $parent;
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

    public function grid()
    {
        return $this->grid;
    }

    /**
     * GridField_ActionMenu
     * GridFieldToolbarHeader
     * GridFieldSortableHeader
     * GridFieldFilterHeader
     * GridFieldDataColumns
     * GridFieldDeleteAction
     * GridFieldViewButton
     * GridFieldEditButton
     * GridFieldExportButton
     * GridFieldPrintButton
     * GridFieldPaginator
     * GridFieldDetailForm
     *
     * GridFieldAddExistingSearchButton
     * GridFieldAddNewInlineButton
     * GridFieldAddNewMultiClass
     * GridFieldEditableColumns
     * GridFieldOrderableRows
     * GridFieldRequestHandler
     * GridFieldTitleHeader
     * GridFieldConfigurablePaginator
     * - GridFieldExternalLink
     * - GridFieldPageCount
     * - GridFieldAddNewButton
     * - GridFieldButtonRow
     * - GridFieldAddExistingAutocompleter
     * - GridFieldFooter
     * - GridFieldStateManager
     * - GridFieldLevelup
     * - GridFieldLazyLoader
     * - GridFieldImportButton
     * - GridFieldGroupDeleteAction
     *
     *
     * GridFieldNestConfig
     * GridFieldManyManyConfig
     * GridFieldManyManyFocusConfig
     */
}
