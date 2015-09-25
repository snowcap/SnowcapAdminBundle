<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class UrlFieldType
 *
 * Add a link surrounding the TextFieldType
 */
class UrlFieldType extends TextFieldType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver
            ->setDefined(array('url'))
            ->setAllowedTypes('url', array('callable', 'string'))
        ;
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

        $url = $field->getOption('url');

        if (is_callable($url)) {
            $url = call_user_func($url, $row);
        }

        $viewContext['url'] = $url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'url';
    }

    /**
     * @return string
     */
    public function getBlockName()
    {
        return 'url';
    }
}
