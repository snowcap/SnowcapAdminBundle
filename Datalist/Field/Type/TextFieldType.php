<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;

class TextFieldType extends AbstractFieldType
{
    /**
     * @return string
     */
    public function getName()
    {
        return 'text';
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setOptional(array('truncate'));
    }

    /**
     * @param ViewContext $viewContext
     * @param DatalistFieldInterface $field
     * @param mixed $row
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistFieldInterface $field, $row, array $options)
    {
        parent::buildViewContext($viewContext, $field, $row, $options);

        if(isset($options['truncate'])) {
            $viewContext['truncate'] = $options['truncate'];
        }
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'text';
    }
}