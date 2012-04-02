<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Snowcap\AdminBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bridge\Doctrine\Form\ChoiceList\EntityChoiceList;
use Symfony\Bridge\Doctrine\Form\EventListener\MergeCollectionListener;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntitiesToArrayTransformer;
use Symfony\Bridge\Doctrine\Form\DataTransformer\EntityToIdTransformer;
use Symfony\Component\Form\AbstractType;

use Snowcap\AdminBundle\Environment;
use Snowcap\AdminBundle\Form\ChoiceList\TranslatableEntityChoiceList;

class TranslatableEntityType extends AbstractType
{
    protected $registry;

    /**
     * @var \Snowcap\AdminBundle\Environment
     */
    protected $admin;

    public function __construct(RegistryInterface $registry,Environment $admin)
    {
        $this->admin = $admin;
        $this->registry = $registry;
    }

    public function getDefaultOptions(array $options)
    {
        $defaultOptions = array(
            'em'                => null,
            'class'             => null,
            'property'          => null,
            'query_builder'     => null,
            'choices'           => null,
        );

        $options = array_replace($defaultOptions, $options);

        if (!isset($options['choice_list'])) {
            $defaultOptions['choice_list'] = new TranslatableEntityChoiceList(
                $this->registry->getEntityManager($options['em']),
                $options['class'],
                $options['property'],
                $options['query_builder'],
                $options['choices'],
                $this->admin
            );
        }

        return $defaultOptions;
    }

    public function getParent(array $options)
    {
        return 'entity';
    }

    public function getName()
    {
        return 'translatable_entity';
    }
}
