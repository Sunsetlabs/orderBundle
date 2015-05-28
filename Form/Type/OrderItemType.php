<?php

namespace Sunsetlabs\OrderBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Services\StockManagerInterface;

class OrderItemType extends AbstractType
{
    protected $em;
    protected $stock_manager;
    protected $order_item_class;
    protected $product_item_class;

	public function __construct(EntityManagerInterface $em, StockManagerInterface $stock_manager, $order_item_class, $product_item_class)
	{
        $this->em = $em;
        $this->stock_manager = $stock_manager;
        $this->order_item_class = $order_item_class;
        $this->product_item_class = $product_item_class;
	}

    public function buildForm(FormBuilderInterface $builder, array $options)
    {	
    	$builder->add('product', 'autocomplete_entity', array(
                    'class' => $this->product_item_class,
                    'update_route' =>  'retrive_products', // TODO: make this url a parameter
                    'constraints' => array(
                        new NotBlank()
                    )
                ))
    			->add('quantity', null, array(
                    'required' => false,
                    'constraints' => array(
                        new NotBlank()
                    )
                ));

        $builder->addEventListener(FormEvents::POST_BIND, array($this, 'postBind'));

    }

    public function postBind(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();
        $old_values = $this->em->getUnitOfWork()->getOriginalEntityData($data);
        $quantity = $data->getQuantity();

        if (is_array($old_values) and !empty($old_values)) {
            if ($old_values['product_id'] == $data->getProduct()->getId()){
                $quantity -= $old_values['quantity'];
            }
        }

        if ($data->getProduct()) {
            $valid = $this->stock_manager->hasStock($data->getProduct()->getId(), $quantity);

            if (!$valid) {
                $form->get('quantity')->addError(new FormError('stock '.$data->getProduct()->getStock(), null, array(), null, 'stock') );
            }
        }
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => $this->order_item_class,
        ));
    }

    public function getName()
    {
        return 'order_item_type';
    }
}