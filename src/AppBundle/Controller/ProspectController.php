<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Prospect;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

/**
 * Prospect controller.
 *
 * @Route("prospect")
 */
class ProspectController extends Controller
{
    /**
     * Lists all prospect entities.
     *
     * @Route("/", name="prospect_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $prospects = $em->getRepository('AppBundle:Prospect')->findAll();

        return $this->render('prospect/index.html.twig', array(
            'prospects' => $prospects,
        ));
    }

    /**
     * Finds and displays a prospect entity.
     *
     * @Route("/{id}", name="prospect_show")
     * @Method("GET")
     */
    public function showAction(Prospect $prospect)
    {

        return $this->render('prospect/show.html.twig', array(
            'prospect' => $prospect,
        ));
    }
}
