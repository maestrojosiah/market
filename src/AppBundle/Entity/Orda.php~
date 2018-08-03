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
     * @var string
     *
     * @ORM\Column(name="order_number", type="string", length=255)
     */
    private $orderNumber;

    /**
     * @ORM\ManyToOne(targetEntity="Prospect", inversedBy="orders")
     * @ORM\JoinColumn(name="prospect_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $prospect;


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
     * Set orderNumber
     *
     * @param string $orderNumber
     *
     * @return Orda
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    /**
     * Get orderNumber
     *
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
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
}
