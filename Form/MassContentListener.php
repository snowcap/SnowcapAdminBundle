<?php
namespace Snowcap\AdminBundle\Form;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\Event\DataEvent;

class MassContentListener implements EventSubscriberInterface {
    protected $builder;
    public function __construct(\Symfony\Component\Form\FormBuilder $builder) {
        $this->builder = $builder;
    }
    static public function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
        );
    }

    public function preSetData(DataEvent $event)
    {
        $form = $event->getForm(); /* @var \Symfony\Component\Form\Form $form */
        $data = $event->getData();
        if (null === $data) {
            $data = array();
        }

        foreach($data as $className => $classValues) {
            $classForm = $this->builder->getFormFactory()->createNamed('form', $className);
            foreach($classValues as $fieldName => $fieldValues) {
                $fieldForm = $this->builder->getFormFactory()->createNamed('form', $fieldName);
                foreach($fieldValues as $entityId => $fieldValue) {
                    $fieldForm->add($this->builder->getFormFactory()->createNamed('hidden', $entityId, $fieldValue, array('attr' => array('class' => 'massupdate'))));
                }
                $classForm->add($fieldForm);
            }
            $form->add($classForm);
        }
    }
    
}