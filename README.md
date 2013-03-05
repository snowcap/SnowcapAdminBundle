Snowcap Admin Bundle
==================================

Warning : this bundle is still pretty much WIP. If you are looking for an admin bundle with a lof of community attention, 
you should probably look at [SonataAdminBundle](https://github.com/sonata-project/SonataAdminBundle).

## Prerequisites

This version of the bundle requires Symfony 2.2+. If you are using Symfony
2.1.x, please use the 2.1.x branch of the bundle.

### Translations

If you wish to use default texts provided in this bundle, you have to make
sure you have translator enabled in your config.

``` yaml
# app/config/config.yml

framework:
    translator: ~
```

For more information about translations, check [Symfony documentation](http://symfony.com/doc/current/book/translation.html).

## Installation

Installation is a 7 step process:

1. Download SnowcapAdminBundle using composer
2. Enable the Bundle and its dependencies
3. Create your admin bundle
4. Enable the admin routing
5. Configure Assetic
6. Configure security
7. Additional configuration steps

### Step 1: Download SnowcapAdminBundle using composer

Add SnowcapAdminBundle in your composer.json:

```js
{
    "require": {
        "snowcap/admin-bundle": "dev-master"
    }
}
```

Now tell composer to download the bundle by running the command:

``` bash
$ php composer.phar update snowcap/admin-bundle
```

Composer will install the bundle to your project's `vendor/snowcap` directory.

### Step 2: Enable the bundle

Enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Snowcap\AdminBundle\SnowcapAdminBundle(),
        // Required dependencies
        new Snowcap\CoreBundle\SnowcapCoreBundle(),
        new Snowcap\BootstrapBundle\SnowcapBootstrapBundle(),
    );
}
```

### Step 3: Create your Admin Bundle

In order to be able to use the Snowcap Admin Bundle, you need to create your own Admin Bundle in your project.

``` bash
$ php ./app/console generate:bundle
```

Your bundle must extend SnowcapAdminBundle in order for it to work.

```php
<?php
// src/Acme/AdminBundle/AcmeAdminBundle.php

public function getParent()
{
    return 'SnowcapAdminBundle';
}
```

### Step 4: Enable admin routing

```yml
# app/config/routing.yml

snowcap_admin:
    resource: "@SnowcapAdminBundle/Resources/config/routing.yml"
    prefix: /admin

```

### Step 5: Configure Assetic

SnowcapAdminBundle uses assetic in order to speed up the display of the admin pages. You must add SnowcapAdminBundle to
the list of configured assetic bundles. Additionally, the lessphp and cssrewrite filters must be enabled for the AdminBundle to work.

```yml
# app/config/config.yml

assetic:
    debug:          "%kernel.debug%"
    use_controller: false
    bundles: ["AcmeSiteBundle", "SnowcapAdminBundle"]
    filters:
        cssrewrite: ~
        lessphp: ~

```

### Step 6: Configure security

The AdminBundle requires at least an active firewall.

If you just want to test it right away on your local machine, you can simply create an anonymous firewall :

```yml
# app/config/security.yml

security:
    firewalls:
        admin:
            pattern: ^/admin
            anonymous: true
```

You can use whichever authentication mechanism you like. In order to make your life easier, SnowcapAdminBundle provides
a base user class, and a few other extras to be used with Doctrine's entity user provider and standard login form authentication.

First, create a user class in your AdminBundle's entity directory:

``` php
<?php
// src/Acme/AdminBundle/Entity/AdminUser.php

namespace Acme\AdminBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Snowcap\AdminBundle\Entity\User;

/**
 * @ORM\Entity
 * @ORM\Table
 */
class AdminUser extends User
{

}
```

You can then change your security.yml config file:

```yml
# app/config/security.yml

security:
    encoders:
        Snowcap\AdminBundle\Entity\User: sha512

    providers:
        admin_users:
            entity: { class: AcmeSiteBunde:Artist, property: username }

    firewalls:
        ...

        admin:
            pattern:    ^/admin
            anonymous: ~
            form_login:
                login_path:  snowcap_admin_login
                check_path:  snowcap_admin_login_check
            logout:
                path: snowcap_admin_logout

    access_control:
        - { path: ^/admin/login, roles: IS_AUTHENTICATED_ANONYMOUSLY }
        - { path: ^/admin, role: ROLE_ADMIN }
```

Don't forget to update your database schema, using schema:update or migrations:diff / migrations:migrate:

``` bash
$ php ./app/console doctrine:schema:update --force
```

When this is done, you can create admin users through the command line:

``` bash
$ php ./app/console snowcap:admin:generate:user
```

Make sure to give to your user at least one admin role as configured in your security.yml file.

You can now access the administration interface.

### Step 7: Additional configuration steps

#### Enable translations

SnowcapAdminBundle stores its own translation messages under the "SnowcapAdminBundle" translation domain. Other interface messages,
such as the title in the navbar, form and datalist labels, are specific to your project, and are translated through a distinct
translation domain. By default, this translation domain is "admin", but you can change it in your project config:

```yml
# app/config/config.yml

snowcap_admin:
    default_translation_domain: backoffice

```

## Creating admin classes

One of the main features of SnowcapAdminBundle is to allow you to create CRUD interfaces that manage entities. We call those CRUD interfaces
"Content Admins".

Creating a Content Admin can be done in 2 steps:

1. Create a Content Admin class
2. Register your admin class with the Service Container

### Create a Content Admin class

The first step is to create an Admin class that extends the abstract ContentAdmin class. You will have to implement at least four methods:

* _getForm_ must return a Symfony/Component/Form/FormInterface instance
* _getDatalist_ must return a Snowcap/AdminBundle/Datalist/DatalistInterface instance
* _getEntityName_ receives an entity as sole argument and must return a textual representation of that entity (its name or its title for instance)
* _getEntityClass_ must return the fully qualified class name of the managed entity

``` php
<?php
// src/Acme/AdminBundle/Admin/ArtistAdmin.php

namespace Acme\AdminBundle\Admin;

use Snowcap\AdminBundle\Admin\ContentAdmin

class ArtistAdmin extends ContentAdmin
{
    /**
     * Return the main admin form for this content
     *
     * @return \Symfony\Component\Form\Form
     */
    public function getForm()
    {
        return $this->formFactory
            ->createBuilder('form', null, array('data_class' => 'Acme\SiteBundle\Entity\Artist'))
            ->add('firstName', 'text')
            ->add('lastName', 'text')
            ->getForm();
    }

    /**
     * Return the main admin list for this content
     *
     * @return \Snowcap\AdminBundle\Datalist\DatalistInterface
     */
    public function getDatalist()
    {
        return $this->datalistFactory
            ->createBuilder('datalist', array('data_class' => 'Acme\SiteBundle\Entity\Artist'))
            ->addField('firstName', 'text')
            ->addField('lastName', 'text')
            ->getDatalist();
    }

    /**
     * @param object $entity
     * @return string
     */
    public function getEntityName($entity)
    {
        return $entity->getName();
    }

    /**
     * @return string
     */
    public function getEntityClass()
    {
        return 'Acme\SiteBundle\Entity\Artist';
    }
}
```

Your admin class is ready but we still need to register it as a service.

## Register your admin class with the Service Container

Simply edit your Admin Bundle services.yml file and declare your Admin Class as a service that extends the

```yml
# src/Acme/AdminBundle/Resources/config/services.yml

class: Acme\AdminBundle\Admin\ArtistAdmin
    parent: snowcap_admin.admin_content
    tags:
        - { name: snowcap_admin.admin, alias: artist, label: Artist|Artists }

```

That's it, your admin class is ready to use. You can test it at http://yourbaseurl/admin/artist.
