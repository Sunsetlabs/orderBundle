<?php

namespace Sunsetlabs\OrderBundle\Entity;

use Sunsetlabs\EcommerceResourceBundle\Interfaces\Order\OrderInterface;
use Sunsetlabs\EcommerceResourceBundle\Interfaces\Order\OrderItemInterface;
use Doctrine\Common\Collections\ArrayCollection;


abstract class Order implements OrderInterface
{
	const STATE_CANCEL = 'cancel';

	protected $id;
	protected $items;
	protected $state;

	function __construct() {
		$this->items = new ArrayCollection();
	}
	public function getStates()
	{
		return array();
	}
	public function getId()
	{
		return $this->id;
	}
	public function getState()
	{
		return $this->state;
	}
	public function setState($state)
	{
		$this->state = $state;
		return $this;
	}
	public function hasState($state)
	{
		return in_array($state, $this->getStates());
	}
	public function getNextState()
	{
		$states = $this->getStates();
		$key = array_search($this->state, $states);

		if (($key !== false) and (isset($states[$key + 1]))) {
		     return $states[$key + 1];
		}else{
		     return false;
		}
	}
	public function getPrevState()
	{
		$states = $this->getStates();
		$key = array_search($this->state, $states);

		if (($key !== false) and (isset($states[$key - 1]))) {
		     return $states[$key - 1];
		}else{
		     return false;
		}
	}
	public function isCanceld()
	{
		return ($this->state === self::STATE_CANCEL);
	}
	public function cancel()
	{
		$this->state = self::STATE_CANCEL;
	}
	public function getItems()
	{
		return $this->items;
	}
	public function addItem(OrderItemInterface $item)
	{
		$i = $this->getItem($item);
		if (!$i) {
			$this->items->add($item);
			$item->setOrder($this);
			return $this;
		}
		$i->merge($item);
		return $this;
	}
	public function removeItem(OrderItemInterface $item, $all = true)
	{
		$key = $this->getItemKey($item);
		if ($key === false) {
			return $this;
		}

		$item->setQuantity(-$item->getQuantity());

		$i = $this->items->get($key);

		if ($all) {
			$this->items->remove($key);
			$i->setOrder();
		}else{
			$i->merge($item);
		}
		return $this;
	}
	public function hasItem(OrderItemInterface $item)
	{
		return ($this->getItem($item) != false);
	}
	public function clear()
	{
		$this->items->clear();
		return $this;
	}
	public function isEmpty()
	{
		return $this->items->isEmpty();
	}
	public function getItem(OrderItemInterface $item)
	{
		foreach ($this->items as $i) {
			if ($i->getIdentifier()->equals($item->getIdentifier())) {
				return $i;
			}
		}
		return false;
	}
	protected function getItemKey(OrderItemInterface $item)
	{
		foreach ($this->items as $key => $i) {
			if ($i->getIdentifier()->equals($item->getIdentifier())) {
				return $key;
			}
		}
		return false;
	}
}