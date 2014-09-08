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
