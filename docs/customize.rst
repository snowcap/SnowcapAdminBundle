Customize
=========

Setting a different default_locale for admin
--------------------------------------------

You can modify the default_locale for the admin in your config.yml file:

.. code-block:: yaml

    # app/config/config.yml

    snowcap_admin:
        default_locale: "nl"
        route_prefix: "/admin"

The "route_prefix" parameter is optional and is set to "/admin" by default. The admin will check if your route
starts with the given route prefix before setting the new default_locale.

Enabling the translation interface
----------------------------------

.. WARNING::

    For this module to work, your translations should not reside in ``app/Resources/transalations`` but rather in 
    your Bundle Resources directory.
    
SnowcapAdminBundle offers a simple translation interface. To enable it, you must first update your configuration file:

.. code-block:: yaml

    # app/config/config.yml
    
    snowcap_admin:
        translation_catalogues: [Acme\DemoBundle\messages, Acme\DemoBundle\validators]
        
You must then add a link in your admin navigation template (you will need to override the default one if 
you haven't done it yet), so that the interface is accessible:

.. code-block:: jinja

    # src/Acme/AdminBundle/Resources/views/Navigation/main.html.twig
    
    <li>
        <a href="{{ path('snowcap_admin_cataloguetranslation_index')}}">
            {{ 'navigation.interfacecontent'|trans({}, 'SnowcapAdminBundle') }}
        </a>
    </li>
