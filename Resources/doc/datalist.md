Working with Datalists
======================

SnowcapAdminBundle is bundled with a powerful Datalist component. Datalists are reprensentation of flat lists of
items, and with SnowcapAdminBundle, you can:

* Create good-looking datalists
* Add actions to your datalists
* Search and filter datalist items

## Creating a datalist

The Datalist API shares many similarities with Symfony's form API.

```php
<?php
// src/Acme/AdminBundle/Controller/CustomController.php

public function customAction()
{
    $datalistFactory = $this->get('snowcap_admin.datalist_factory');
    $datalist = $datalistFactory
        ->createBuilder('datalist')
        ->addField('name', 'text')
        ->getDatalist();

    $dataSource = new ArrayDatasource(array(
        array('name' => 'Starcraft2'),
        array('name' => 'Diablo 3'),
    ));
    $datalist->setDatasource($dataSource);
    // render the view
}
```

As you can see, creating the datalist is a three step process:

1. Create a datalist builder
2. Add fields
3. Create a Datasource and attach it to the datalist

To display your Datalist in your template:

```twig
{# src/Acme/AdminBundle/Resources/views/Custom/custom.html.twig #}


{{ datalist_widget(datalist) }}
```

## Datalist Types

Datalist types are used to build Datalist instances. SnowcapAdminBundle provides only one built-in type :
the _datalist_ Datalist Type. If you want to create a datalist "by hand", you will probably end up using
this type. You can also easily create your own datalist type.

### Datalist Datalist Type (datalist)

The _datalist_ Datalist type is the most basic type you can use.

#### Options

##### data_class

*type:* string *default:* null

If the datalist expects rows to be objects, then you must specify a data_class option to tell the datalist component
which class of objects to expect.

If your datalist have simple array rows, this option must remain null.

#### limit_per_page
*type:* integer *default:* null

When specified, it will trigger the pagination of the Datalist and will use the option to limit the
number of rows to display per page.

#### range_limit
*type:* integer *default:* 10

When the pagination is active, this option controls the maximum number of pagination links to display
in the pagination widget.

#### search_placeholder
*type:* string *default:* datalist.search.placeholder

Controls the text of the search placeholder.

##### search_submit
*type:* string *default:* datalist.search.submit

Controls the text of the search submit button

##### filter_submit
*type:* string *default:*: datalist.filter.submit

Controls the text of the filter submit button

##### filter_reset
*type:* string *default:* datalist.filter.reset

Controls the text of the filter reset button

##### translation_domain
*type:* string *default:* messages

Changes the translation domain used by the datalist to translated labels.

## Datalist Field Types

### Abstract Field Type

The AbstractFieldType is the base class for all field types. While it cannot be used on its own, it already defines a
few options used by all field types.

#### Options

##### property_path
*type:* string *default:* null

The property path to use in order to compute the field value for a given row. For more information on how property
paths work, please refer to the [PropertyAccess component](http://symfony.com/doc/current/components/property_access/introduction.html).

If no property_path is provided, the name of the field will be used.

##### callback
*type:* callable *default:* null

If a valid callback is provided for this option, the callback will be used to process the value before passing it to the template.

##### default
*type:* mixed *default:* null

The default value to use when the computed value of the field for a given property is null.

### Text Field Type (text)

The text field type is the most common field type you can use in a datalist. It simply displays text.

#### Options

See Abstract Field Type for inherited options.

### Heading Field Type (heading)

The heading field type has the exact same behaviour than the text field type, but is displayed slightly differently by default.

#### Options

See Abstract Field Type for inherited options.

### DateTime Field Type (datetime)

The Datetime field type is used to display formatted dates. The underlying value must be a valid DateTime object.

#### Options

##### format
*type:* string *default:* d/m/Y

The format string to use. See [PHP documentation for DateTime::format()](http://php.net/manual/fr/datetime.format.php) for more information.

See also Abstract Field Type for inherited options.

### Label Field Type

The label field type is particularly useful when dealing with "choices" values (for example, a property that can have 3 or 4
possible values). It allows you to control how the value will be displayed.

#### Options

##### mappings
*type:* array *default:* null

This option is required, and must be provided as an associative array whose keys correspond to the possible values of the
property, and whose values must also be an associative array. This associative array has two keys :

* label: the label to display for the given property value
* attr: an associative array of html attributes used for rendering (to specify a class for example)

```php
$builder->addField('type', 'label', array(
    'mappings' => array(
        'rts' => array(
            'label' => 'Real-time strategy',
            'attr' => array('class' => 'game-rts')
        ),
        'fps' => array(
            'label' => 'First-person shooter',
            'attr' => array('class' => 'game-fps')
        ),
        'rpg' => array(
            'label' => 'Role-playing game',
            'attr' => array('class' => 'game-rpg')
        ),
    )
));
```

See also Abstract Field Type for inherited options.

## Action types

## Search types

## Filter types

## Customize Datalist rendering
