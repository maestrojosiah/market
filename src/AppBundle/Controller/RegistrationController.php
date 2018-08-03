<?php 

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use AppBundle\Entity\User;
use AppBundle\Entity\Presentation;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\HttpFoundation\JsonResponse;

class RegistrationController extends Controller
{
    /**
     * @Route("/register", name="user_registration")
     */
    public function registerAction(Request $request, UserPasswordEncoderInterface $passwordEncoder)
    {
        // 1) build the form
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        // 2) handle the submit (will only happen on POST)
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setAdmin(0);
            $user->setActive(0);
            // 3) Encode the password (you could also do this via Doctrine listener)
            $password = $passwordEncoder->encodePassword($user, $user->getPlainPassword());
            $user->setPassword($password);

            // 4) save the User!
            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // ... do any other work - like sending them an email, etc
            // maybe set a "flash" success message for the user

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'registration/register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/register/helper", name="book_registration_helper")
     */
    public function helperAction(Request $request)
    {

        $array = $this->getArray();
        $em = $this->getDoctrine()->getManager();

        foreach ($array as $key => $presentation_data) {
            $presentation = new Presentation();
            $presentation_user = $em->getRepository('AppBundle:User')->find($presentation_data['user_id']);
            $presentation_book = $em->getRepository('AppBundle:Book')->find($presentation_data['book_id']);
            $presentation->setBook($presentation_book);
            $presentation->setPhotoPath($presentation_data['photo_path']);
            $presentation->setDescription($presentation_data['description']);
            $presentation->setDeleted($presentation_data['deleted']);
            $presentation->setUser($presentation_user);

            $em = $this->getDoctrine()->getManager();
            // $em->persist($presentation);
            // $em->flush();
            
        }


        return $this->render(
            'registration/renew.html.twig',
            ['array' => $array]
        );
    }

    /**
     * @Route("/admin/register/list", name="registration_list")
     */
    public function listAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $users = $em->getRepository('AppBundle:User')->findAll();
        return $this->render(
            'registration/list.html.twig',
            ['users' => $users]
        );
    }

    /**
     * @Route("/admin/update/{id}/{column}", name="update_user")
     */
    public function updateFigureAction(Request $request, $column, $id)
    {

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository('AppBundle:User')->find($id);
        $string_get_command = "get".$column;
        $string_set_command = "set".$column;
        if($user->$string_get_command() == 1){
            $change_to = 0;
        } else {
            $change_to = 1;
        }
        $user->$string_set_command($change_to);
        $this->save($user);
        return $this->redirectToRoute('registration_list');
    }

    private function getArray() {
        $array = Array();
        return $array;

    }

    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }


    private function save($entity){
        $this->em()->persist($entity);
        $this->em()->flush();
        return true;
    }

}