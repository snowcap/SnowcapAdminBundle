Working with Datalists
======================

SnowcapAdminBundle is bundled with a powerful Datalist component. Datalists are reprensentation of flat lists of items, and with SnowcapAdminBundle, you can:

* Create good-looking datalists
* Add actions to your datalists
* Search and filter datalist items

Creating a datalist
-------------------

The Datalist API shares many similarities with Symfony's form API.

.. code-block:: php

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

As you can see, creating the datalist is a three step process:

1. Create a datalist builder
2. Add fields
3. Create a Datasource and attach it to the datalist

To display your Datalist in your template:

.. code-block:: jinja

    {# src/Acme/AdminBundle/Resources/views/Custom/custom.html.twig #}

    {{ datalist_widget(datalist) }}

Datalist Types
--------------

Datalist types are used to build Datalist instances. SnowcapAdminBundle provides only one built-in type : the _datalist_ Datalist Type. If you want to create a datalist "by hand", you will probably end up using this type. You can also easily create your own datalist type.

Datalist Datalist Type (datalist)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The *datalist* Datalist type is the most basic type you can use.

Options
~~~~~~~

====================    ========    ================================    ================================
Name                    Type        Default                             Description
====================    ========    ================================    ================================
data_class              string      null                                If the datalist expects rows to be objects, then you must specify a data_class option to tell the datalist component which class of objects to expect. If your datalist have simple array rows, this option must remain null.
limit_per_page          integer     null                                When specified, it will trigger the pagination of the Datalist and will use the option to limit the number of rows to display per page.
range_limit             integer     10                                  When the pagination is active, this option controls the maximum number of pagination links to display in the pagination widget.
search_placeholder      string      datalist.search.placeholder         Controls the text of the search placeholder.
search_submit           string      datalist.search.submit              Controls the text of the search submit button.
filter_submit           string      datalist.filter.submit              Controls the text of the filter submit button.
filter_reset            string      datalist.filter.reset               Controls the text of the filter reset button.
translation_domain      string      messages                            Changes the translation domain used by the datalist to translated labels.
====================    ========    ================================    ================================

Datalist Field Types
--------------------

Abstract Field Type
~~~~~~~~~~~~~~~~~~~

The AbstractFieldType is the base class for all field types. While it cannot be used on its own, it already defines a few options used by all field types.

**Options**

====================    ========    ================================    ================================
Name                    Type        Default                             Description
====================    ========    ================================    ================================
property_path           string      null                                The property path to use in order to compute the field value for a given row. For more information on how property paths work, please refer to the `PropertyAccess component <http://symfony.com/doc/current/components/property_access/introduction.html>`_. If no property_path is provided, the name of the field will be used.
callback                callable    null                                If a valid callback is provided for this option, the callback will be used to process the value before passing it to the template.
default                 mixed       null                                The default value to use when the computed value of the field for a given property is null.
====================    ========    ================================    ================================

Text Field Type (text)
~~~~~~~~~~~~~~~~~~~~~~

The text field type is the most common field type you can use in a datalist. It simply displays text.

**Options**

See Abstract Field Type for inherited options.

Heading Field Type (heading)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The heading field type has the exact same behaviour than the text field type, but is displayed slightly differently by default.

**Options**

See Abstract Field Type for inherited options.

DateTime Field Type (datetime)
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~

The Datetime field type is used to display formatted dates. The underlying value must be a valid DateTime object.

**Options**

====================    ========    ================================    ================================
Name                    Type        Default                             Description
====================    ========    ================================    ================================
format                  string      d/m/Y                               The format string to use. See `PHP documentation for DateTime::format() <http://php.net/manual/fr/datetime.format.php>`_ for more information.
====================    ========    ================================    ================================

See also Abstract Field Type for inherited options.

Label Field Type
~~~~~~~~~~~~~~~~

The label field type is particularly useful when dealing with "choices" values (for example, a property that can have 3 or 4
possible values). It allows you to control how the value will be displayed.

**Options**

**mappings**
*type:* array *default:* null

This option is required, and must be provided as an associative array whose keys correspond to the possible values of the
property, and whose values must also be an associative array. This associative array has two keys :

* label: the label to display for the given property value
* attr: an associative array of html attributes used for rendering (to specify a class for example)

.. code-block:: php

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

See also Abstract Field Type for inherited options.

Action types
------------

Search types
------------

Filter types
------------

Customize Datalist rendering
----------------------------

Before creating your own theme for your datalist, check the existing ones to see if there is a match with your needs.

If you want to override the layout of the datalist, you have to create your own layout.

You can extend an existing one if you want to change only a part of it :

.. code-block:: jinja

    {# src/Acme/AdminBundle/Resources/views/Datalist/datalist_custom_layout.html.twig #}

    {% extends 'SnowcapAdminBundle:Datalist:datalist_grid_layout.html.twig' %}

    {% block datalist %}
        {% if datalist.option('search') %}
            {{ datalist_search(datalist) }}
        {% endif %}
        {% if datalist.filterable %}
            <div class="row-fluid">
                <div class="span9">
                    {{ block('datalist_custom') }}
                </div>
                <div class="span3">{{ datalist_filters(datalist) }}</div>
            </div>
        {% else  %}
            {{ block('datalist_custom') }}
        {% endif %}

        {% if datalist.paginator is not null %}
            {{ paginator_widget(datalist.paginator) }}
        {% endif %}
    {% endblock datalist %}

    {% block datalist_custom %}
        {% for item in datalist %}
            <div>
                {% for field in datalist.fields %}
                    {{ datalist_field(field, item) }}
                {% endfor %}
                <p>
                {% if datalist.actions|length > 0 %}
                    {% for action in datalist.actions %}
                        {{ datalist_action(action, item) }}{% if not loop.last %} {% endif %}
                    {% endfor %}
                {% endif %}
                </p>
            </div>
        {% endfor %}
    {% endblock datalist_tiled %}

    {# text field #}
    {% block text_field %}
        <h4>{{ field.options['label']|trans({}, translation_domain) }}</h4>
        <p>
            {% if value is not null %}
                {{ value|raw }}
            {% else %}
                <span class="empty-value">{{ "datalist.empty_value"|trans({}, "SnowcapAdminBundle") }}</span>
            {% endif %}
        </p>
    {% endblock text_field %}

Now you just have to apply the theme on your datalist. See the example below :

.. code-block:: jinja

    {# src/Acme/AdminBundle/Resources/views/Custom/custom.html.twig #}

    {% datalist_theme datalist 'AcmeAdminBundle:Datalist:datalist_tiled_layout.html.twig' %}

    {{ datalist_widget(datalist) }}

