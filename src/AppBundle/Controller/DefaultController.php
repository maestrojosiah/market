<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\JsonResponse;
class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        $display_books = $this->getRandomDoctrineItem($this->em(), 'AppBundle\Entity\Book', 8);
        return $this->render('default/index.html.twig', ['display_books' => $display_books]);
    }

    /**
     * @Route("/hhes/about", name="about")
     */
    public function aboutAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/about.html.twig');
    }

    /**
     * @Route("/hhes/agent/find", name="find_agent")
     */
    public function findAction(Request $request)
    {
        $agents = $this->em()->getRepository('AppBundle:User')
            ->findBy(
                array('active' => true, 'category' => 'le'),
                array('residence' => 'ASC')
            );
        $compressed_list = [];
        foreach ($agents as $key => $agent) {
            $compressed_list[$agent->getResidence()] = $agent;
        }
        // replace this example code with whatever you need
        return $this->render('default/find_agent.html.twig', ['agents' => $compressed_list]);
    }

    /**
     * @Route("/hhes/agent/list", name="list_agent")
     */
    public function listAgentsAction(Request $request)
    {
        $residence = $request->request->get('residence');
    
        $agents = $this->em()->getRepository('AppBundle:User')
            ->searchMatchingResidents($residence);

        $agents_list = [];
        foreach ($agents as $key => $agent) {
            $agents_list[] = [
                $agent->getFName()." ".$agent->getLName(), 
                $agent->getPhone(), 
                $agent->getEmail(), 
                $agent->getUsername().".hheskenya.org",
                $agent->getResidence() 
             ];
        }
        return new JsonResponse($agents_list);
    }

    /**
     * @Route("/hhes/books/list", name="search_books")
     */
    public function searchBooksAction(Request $request)
    {
        $result_text = $request->request->get('search_text');
    
        $books = $this->em()->getRepository('AppBundle:Book')
            ->searchMatchingBooks($result_text);

        $result_text = [];
        foreach ($books as $key => $book) {
            $result_text[] = [
                $book->getTitle(), 
                $book->getAuthor(), 
                $book->getCategory(), 
                substr($book->getDescription(), 0, 100)."...",
                $this->generateUrl("book_show", ['id' => $book->getId()])
             ];
        }
        return new JsonResponse($result_text);
    }

    /**
     * Retrieve one random item of given class from ORM repository.
     * 
     * @param EntityManager $em    The Entity Manager instance to use
     * @param string        $class The class name to retrieve items from
     * @return object
     */ 
    function getRandomDoctrineItem(EntityManager $em, $class, $limit)
    {
        static $counters = [];
        if (!isset($counters[$class])) {
            $counters[$class] = (int) $em->createQuery(
                'SELECT COUNT(c) FROM '. $class .' c' 
            )->getSingleScalarResult();
        }
        // return $counters;
        return $em
            ->createQuery('SELECT c FROM ' . $class .' c ORDER BY c.id ASC')
            ->setMaxResults($limit)
            ->setFirstResult(mt_rand(0, $counters[$class] - 1))
            ->getResult()
        ;
    }
    
    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }


}
