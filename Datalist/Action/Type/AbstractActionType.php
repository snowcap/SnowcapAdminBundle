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
        $resolver
            ->setDefaults(array(
                'attr' => array(),
                'enabled' => true,
            ))
            ->setAllowedTypes(array(
                'enabled' => array('bool', 'callable')
            ));
    }

    /**
     * @param \Snowcap\AdminBundle\Datalist\ViewContext $viewCobtext
     * @param \Snowcap\AdminBundle\Datalist\Field\DatalistFieldInterface $field
     * @param mixed $value
     * @param array $options
     */
    public function buildViewContext(ViewContext $viewContext, DatalistActionInterface $action, $item, array $options)
    {
        $viewContext['attr'] = $options['attr'];

        $enabled = $options['enabled'];
        if(is_callable($enabled)) {
            $enabled = call_user_func($enabled, $item);
        }
        if(!is_bool($enabled)) {
            throw new \UnexpectedValueException('The "enabled" callback must return a boolean value');
        }
        $viewContext['enabled'] = $enabled;

        $url = $action->getType()->getUrl($action, $item, $action->getOptions());

        $viewContext['url'] = $url;
        $viewContext['label'] = $action->getOption('label');
        $viewContext['translation_domain'] = $action->getDatalist()->getOption('translation_domain');
        $viewContext['options'] = $options;
    }
}