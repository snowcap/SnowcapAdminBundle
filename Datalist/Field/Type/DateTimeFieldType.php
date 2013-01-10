<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

class DateTimeFieldType extends AbstractFieldType
{
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults(array(
            'format' => 'd/m/Y'
        ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewContext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $value, array $options)
    {
        parent::buildViewContext($viewContext, $field, $value, $options);

        $viewContext['format'] = $options['format'];
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'datetime';
    }
}