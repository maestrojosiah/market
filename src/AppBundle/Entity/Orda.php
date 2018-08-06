<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Orda
 *
 * @ORM\Table(name="orda")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\OrdaRepository")
 */
class Orda
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
     * @ORM\ManyToOne(targetEntity="Prospect", inversedBy="orders")
     * @ORM\JoinColumn(name="prospect_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $prospect;

    /**
     * @ORM\OneToMany(targetEntity="Purchase", mappedBy="order")
     */
    private $purchases;


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
     * Constructor
     */
    public function __construct()
    {
        $this->purchases = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set prospect
     *
     * @param \AppBundle\Entity\Prospect $prospect
     *
     * @return Orda
     */
    public function setProspect(\AppBundle\Entity\Prospect $prospect = null)
    {
        $this->prospect = $prospect;

        return $this;
    }

    /**
     * Get prospect
     *
     * @return \AppBundle\Entity\Prospect
     */
    public function getProspect()
    {
        return $this->prospect;
    }

    /**
     * Add purchase
     *
     * @param \AppBundle\Entity\Purchase $purchase
     *
     * @return Orda
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
}
