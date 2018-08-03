<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Official;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Official controller.
 *
 * @Route("official")
 */
class OfficialController extends Controller
{
    /**
     * Lists all official entities.
     *
     * @Route("/", name="official_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $officials = $em->getRepository('AppBundle:Official')->findAll();

        return $this->render('official/index.html.twig', array(
            'officials' => $officials,
        ));
    }

    /**
     * Creates a new official entity.
     *
     * @Route("/new", name="official_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $official = new Official();
        $officials = $this->em()->getRepository('AppBundle:Official')->findAll();
        $form = $this->createForm('AppBundle\Form\OfficialType', $official);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($official);
            $em->flush();

            return $this->redirectToRoute('official_show', array('id' => $official->getId()));
        }

        return $this->render('official/new.html.twig', array(
            'official' => $official,
            'officials' => $officials,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a official entity.
     *
     * @Route("/{id}", name="official_show")
     * @Method("GET")
     */
    public function showAction(Official $official)
    {
        $deleteForm = $this->createDeleteForm($official);

        return $this->render('official/show.html.twig', array(
            'official' => $official,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing official entity.
     *
     * @Route("/{id}/edit", name="official_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Official $official)
    {
        $deleteForm = $this->createDeleteForm($official);
        $editForm = $this->createForm('AppBundle\Form\OfficialType', $official);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('official_edit', array('id' => $official->getId()));
        }

        return $this->render('official/edit.html.twig', array(
            'official' => $official,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a official entity.
     *
     * @Route("/{id}", name="official_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Official $official)
    {
        $form = $this->createDeleteForm($official);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($official);
            $em->flush();
        }

        return $this->redirectToRoute('official_index');
    }

    /**
     * Creates a form to delete a official entity.
     *
     * @param Official $official The official entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Official $official)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('official_delete', array('id' => $official->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

    private function user(){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $user;
    }

}
