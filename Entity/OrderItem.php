<?php

namespace Sunsetlabs\OrderBundle\Entity;

use Sunsetlabs\EcommerceResourceBundle\Interfaces\Order\OrderItemInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Order\OrderInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Product\ProductInterface;
use Sunsetlabs\EcommerceResourceBundle\Utils\Identifier;

abstract class OrderItem implements OrderItemInterface
{
	public function getId()
	{
		return $this->id;
	}
	public function getProduct()
	{
		return $this->product;
	}
	public function setProduct(ProductInterface $product)
	{
		$this->product = $product;
		return $this;
	}
	public function getIdentifier()
	{
		$i = new Identifier();
		if ($this->getProduct()){
			$i->addPk('product_id', $this->getProduct()->getId());
		}else{
			$i->addPk('order_item_id', 0);
		}
		return $i;
	}
	public function merge(OrderItemInterface $item)
	{
		$this->setQuantity($this->getQuantity() + $item->getQuantity());
	}
	public function setQuantity($quantity = 0)
	{
		$this->quantity = $quantity;
		return $this;
	}
	public function getQuantity()
	{
		return $this->quantity;
	}
	public function setOrder(OrderInterface $order = null)
	{
		$this->order = $order;
		return $this;
	}
	public function getOrder()
	{
		return $this->order;
	}
}