<?php

namespace Snowcap\AdminBundle\Datalist\Action\Type;

use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Snowcap\AdminBundle\Datalist\ViewContext;
use Snowcap\AdminBundle\Datalist\Action\DatalistActionInterface;

abstract class AbstractActionType implements ActionTypeInterface {
    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {

    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewCobtext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistActionInterface $action, $item, array $options)
    {
        $url = $action->getType()->getUrl($action, $item, $action->getOptions());

        $viewContext['url'] = $url;
        $viewContext['label'] = $action->getOption('label');
        $viewContext['translation_domain'] = $action->getDatalist()->getOption('translation_domain');
        $viewContext['options'] = $options;
    }

}