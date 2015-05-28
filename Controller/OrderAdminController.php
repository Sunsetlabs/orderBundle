<?php

namespace Sunsetlabs\OrderBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Services\OrderManagerInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Services\StockManagerInterface;


class OrderAdminController
{
	protected $em;
	protected $templating;
	protected $formFactory;
	protected $router;
	protected $om;
	protected $sm;

	public function __construct(EntityManager $em, EngineInterface $templating, FormFactoryInterface $formFactory, RouterInterface $router, OrderManagerInterface $order_manager, StockManagerInterface $stock_manager)
	{
		$this->em = $em;
		$this->templating = $templating;
		$this->formFactory = $formFactory;
		$this->router = $router;
		$this->om = $order_manager;
		$this->sm = $stock_manager;
	}

    public function newAction(Request $request)
    {
		$order = $this->getOrder();
		$form = $this->formFactory->create('order_type', $order);

		$old_values = $this->sm->getProductQuantities($order);
		$form->handleRequest($request);
		
		if ($form->isValid())
		{
			$new_values = $this->sm->getProductQuantities($order);
			$this->sm->manageStock($old_values, $new_values);
			$this->em->persist($order);
			$this->em->flush();
			return new RedirectResponse($this->router->generate('admin', array(
			            'action' => 'list',
			            'entity' => 'Order',
			            'view'   => 'list'
			        )));
		}

	    return $this->templating->renderResponse('@SunsetlabsOrder/Forms/order.html.twig', array('form' => $form->createView(), 'fields' => $this->om->getExtraFields()));
    }

    public function editAction(Request $request, $id = null)
    {   
        if (!$id) {
            $id = $request->query->get('id');
        }

        $order = $this->getOrder($id);
        $form = $this->formFactory->create('order_type', $order);

		$old_values = $this->sm->getProductQuantities($order);
        $form->handleRequest($request);

		if ($form->isValid())
		{
			$new_values = $this->sm->getProductQuantities($order);
			$this->sm->manageStock($old_values, $new_values);
			$this->em->persist($order);
			$this->em->flush();
			return new RedirectResponse($this->router->generate('edit_order', array('id' => $order->getId())));
		}

	    return $this->templating->renderResponse('@SunsetlabsOrder/Forms/order.html.twig', array('form' => $form->createView(), 'fields' => $this->om->getExtraFields()));
    }

    protected function getOrder($id = null)
    {
    	if (!$id) {
    		return $this->om->getNewOrder();
    	}
    	return $this->om->getOrder($id);
    }
}