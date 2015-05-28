<?php

namespace Sunsetlabs\OrderBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Count;

class OrderType extends AbstractType
{
	protected $oc;

	public function __construct($oc)
	{
		$this->oc = $oc;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    	$builder->add('items', 'collection', array(
    			'type' => 'order_item_type',
    			'allow_add' => true,
    			'allow_delete' => true,
    			'by_reference' => false,
                'cascade_validation' => true,
                'constraints' => array(
                    new Count(array('min' => 1, 'minMessage' => 'La orden no debe estar vacia.'))
                )
    	));
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->oc
        ));
    }

    public function getName()
    {
        return 'order_type';
    }
}