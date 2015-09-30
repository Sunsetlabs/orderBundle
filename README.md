### Instlacion

#### via composer

````json
{
    "require": {
        "sunsetlabs/order-bundle" : "dev-master"
    },
    "repositories" : [
        {
            "type" : "vcs",
            "url"  : "https://github.com/Sunsetlabs/orderBundle.git"
        }
    ]
}
````

### Configuracion

Registrar en el kernel de la aplicacion

````php
<?php
// app/AppKernel.php

$bundles = array(
    new Sunsetlabs\OrderBundle\SunsetlabsOrderBundle()
);
````

El plugin provee dos clases Order y OrderItem la cuales pueden ser extendidas. La forma mas basica:

````php
// AppBundle/Entity/Order.php
<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sunsetlabs\OrderBundle\Entity\Order as BaseOrder;

/**
 * @ORM\Entity()
 */
class Order extends BaseOrder
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;

	/**
     * @ORM\OneToMany(targetEntity="OrderItem", mappedBy="order", cascade={"persist", "remove"}, orphanRemoval=true)
     */
	protected $items;


     /**
     * @ORM\Column(type="string", length=100)
     */
     protected $state;
}


// AppBundle/Entity/OrderItem.php


namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Sunsetlabs\OrderBundle\Entity\OrderItem as BaseOrderItem;

/**
 * @ORM\Entity()
 */
class OrderItem extends BaseOrderItem
{
	/**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
	protected $id;

	/**
     * @ORM\Column(type="integer")
     */
	protected $quantity;

	/**
     * @ORM\ManyToOne(targetEntity="Order", inversedBy="items")
     * @ORM\JoinColumn(name="my_order_id", referencedColumnName="id")
     */
	protected $order;

	/**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     **/
	protected $product;
}


````
