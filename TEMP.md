field -- scaffoldFormField
remove -- removeByName
fields(['TabName' => [...Fields]]) -- addFieldsToTab
dataField -- dataFieldByName

removeAll -- *removeByName /// remove all fields
removeAllCurrent -- *removeByName /// remove all fields based on the current object/page
removeAllInTab -- *removeByName /// remove all fields in tab
require([...FieldsNames]) -- setRequireFields ///
addError -- * /// adds custom error

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
