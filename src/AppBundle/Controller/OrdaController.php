<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Orda;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Orda controller.
 *
 * @Route("orda")
 */
class OrdaController extends Controller
{
    /**
     * Lists all orda entities.
     *
     * @Route("/", name="orda_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $ordas = $em->getRepository('AppBundle:Orda')->findAll();

        return $this->render('orda/index.html.twig', array(
            'ordas' => $ordas,
        ));
    }

    /**
     * Finds and displays a orda entity.
     *
     * @Route("/{id}", name="orda_show")
     * @Method("GET")
     */
    public function showAction(Orda $orda)
    {

        return $this->render('orda/show.html.twig', array(
            'orda' => $orda,
        ));
    }
}
