<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Prospect
 *
 * @ORM\Table(name="prospect")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProspectRepository")
 */
class Prospect
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="residence", type="string", length=255)
     */
    private $residence;

    /**
     * @ORM\ManyToOne(targetEntity="User", inversedBy="prospects")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="prospect")
     */
    private $purchases;

    /**
     * @ORM\OneToMany(targetEntity="Orda", mappedBy="prospect")
     */
    private $orders;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Prospect
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Prospect
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return Prospect
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set residence
     *
     * @param string $residence
     *
     * @return Prospect
     */
    public function setResidence($residence)
    {
        $this->residence = $residence;

        return $this;
    }

    /**
     * Get residence
     *
     * @return string
     */
    public function getResidence()
    {
        return $this->residence;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->purchases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set user
     *
     * @param \AppBundle\Entity\User $user
     *
     * @return Prospect
     */
    public function setUser(\AppBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     *
     * @return Prospect
     */
    public function addPurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases[] = $purchase;

        return $this;
    }

    /**
     * Remove purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     */
    public function removePurchase(\AppBundle\Entity\Purchase $purchase)
    {
        $this->purchases->removeElement($purchase);
    }

    /**
     * Get purchases
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPurchases()
    {
        return $this->purchases;
    }

    /**
     * Add order
     *
     * @param \AppBundle\Entity\Orda $order
     *
     * @return Prospect
     */
    public function addOrder(\AppBundle\Entity\Orda $order)
    {
        $this->orders[] = $order;

        return $this;
    }

    /**
     * Remove order
     *
     * @param \AppBundle\Entity\Orda $order
     */
    public function removeOrder(\AppBundle\Entity\Orda $order)
    {
        $this->orders->removeElement($order);
    }

    /**
     * Get orders
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrders()
    {
        return $this->orders;
    }
}
