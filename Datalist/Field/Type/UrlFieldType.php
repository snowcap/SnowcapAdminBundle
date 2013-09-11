<?php

namespace Snowcap\AdminBundle\Datalist\Field\Type;

use Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface;
use Snowcap\AdminBundle\Datalist\Field\Type\TextFieldType;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Class UrlFieldType
 *
 * Add a link surrounding the TextFieldType
 */
class UrlFieldType extends TextFieldType
{
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver
            ->setOptional(array(
                'url',
            ))
            ->setAllowedTypes(array(
                'url' => array(
                    'callable', 'string'
                )
            ));
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
