<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\TypeInterface;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;

interface FieldTypeInterface extends TypeInterface
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver);

    /**
     * @return string
     */
    public function getName();

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewCobtext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $value, array $options);
}