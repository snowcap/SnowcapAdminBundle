<?php

namespace Snowcap\AdminBundle\Datalist\Filter\Type;

use Symfony\Component\Form\FormBuilderInterface;

use Snowcap\AdminBundle\Datalist\TypeInterface;
use Snowcap\AdminBundle\Datalist\Filter\DatalistFilterInterface;

interface FilterTypeInterface extends TypeInterface
{
    public function buildForm(FormBuilderInterface $builder, DatalistFilterInterface $filter, array $options);
}