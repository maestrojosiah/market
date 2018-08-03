<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Book;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManager;

/**
 * Book controller.
 *
 * @Route("book")
 */
class BookController extends Controller
{
    /**
     * Lists all book entities.
     *
     * @Route("/", name="book_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findBy(
            array('deleted' => 0),
            array('id' => 'ASC')
        );

        return $this->render('book/index.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Lists all book entities.
     *
     * @Route("/admin/deleted", name="book_deleted")
     * @Method("GET")
     */
    public function deletedAction()
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findBy(
            array('deleted' => 1),
            array('id' => 'ASC')
        );

        return $this->render('book/restore.html.twig', array(
            'books' => $books,
        ));
    }

    /**
     * Lists all book entities.
     *
     * @Route("/author/{author}", name="book_author")
     * @Method("GET")
     */
    public function authorAction($author)
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findBy(
            array('author' => $author),
            array('id' => 'ASC')
        );

        return $this->render('book/author.html.twig', array(
            'books' => $books,
            'author' => $author,
        ));
    }

    /**
     * Lists all book entities.
     *
     * @Route("/category/{category}", name="book_category")
     * @Method("GET")
     */
    public function categoryAction($category)
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findBy(
            array('category' => $category),
            array('id' => 'ASC')
        );

        return $this->render('book/category.html.twig', array(
            'books' => $books,
            'category' => $category,
        ));
    }

    /**
     * Lists all book entities.
     *
     * @Route("/availability/{availability}", name="book_availability")
     * @Method("GET")
     */
    public function availableAction($availability)
    {
        $em = $this->getDoctrine()->getManager();

        $books = $em->getRepository('AppBundle:Book')->findBy(
            array('availability' => $availability),
            array('id' => 'ASC')
        );

        return $this->render('book/availability.html.twig', array(
            'books' => $books,
            'availability' => $availability,
        ));
    }

    /**
     * Creates a new book entity.
     *
     * @Route("/admin/new", name="book_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $book = new Book();
        $form = $this->createForm('AppBundle\Form\BookType', $book);
        $form->handleRequest($request);
        $user = $this->user();

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            $category = $form->get('category')->getData();
            $now = date("d-m-Y h:i:s");
            $book->setUploaded(new \DateTime($now));            
            $book->setDeleted(0);            
            $book_title = $form->get('title')->getData();
            $trimmed_title = str_replace(" ", "_", $book_title);
            $originalName = $image->getClientOriginalName();;
            $filepath = $this->get('kernel')->getProjectDir()."/web/img/books/$category/$trimmed_title/";
            $image->move($filepath, $originalName);
            $simple_filepath = "/img/books/$category/$trimmed_title/";
            $book->setImage($simple_filepath . $originalName);
            $book->setUser($user);
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();

            return $this->redirectToRoute('book_show', array('id' => $book->getId()));
        }

        return $this->render('book/new.html.twig', array(
            'book' => $book,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a book entity.
     *
     * @Route("/{id}", name="book_show")
     * @Method("GET")
     */
    public function showAction(Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);

        $related_books = $this->getRandomDoctrineItem($this->em(), 'AppBundle\Entity\Book', $book->getCategory(), 3);

        return $this->render('book/show.html.twig', array(
            'book' => $book,
            'delete_form' => $deleteForm->createView(),
            'related_books' => $related_books,
        ));
    }

    /**
     * Displays a form to edit an existing book entity.
     *
     * @Route("/admin/{id}/edit", name="book_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Book $book)
    {
        $deleteForm = $this->createDeleteForm($book);
        $editForm = $this->createForm('AppBundle\Form\BookType', $book);
        $editForm->handleRequest($request);
        $formerFileName = $book->getImage();

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $image = $editForm->get('image')->getData();
            $category = $editForm->get('category')->getData();
            $now = date("d-m-Y h:i:s");
            $book->setUploaded(new \DateTime($now));            
            $book_title = $editForm->get('title')->getData();
            $trimmed_title = str_replace(" ", "_", $book_title);
             // if (is_object($image)){
                $book_title = $editForm->get('title')->getData();
                $trimmed_title = str_replace(" ", "_", $book_title);
                $originalName = $image->getClientOriginalName();;
                $filepath = $this->get('kernel')->getProjectDir()."/web/img/books/$category/$trimmed_title/";
                $simple_filepath = "/img/books/$category/$trimmed_title/";
                $image->move($filepath, $originalName);
                $book->setImage($simple_filepath . $originalName);
            // } else {
            //     $config->setImage($formerFileName);
            // }
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('book_edit', array('id' => $book->getId()));
        }

        return $this->render('book/edit.html.twig', array(
            'book' => $book,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/admin/{id}", name="book_delete")
     */
    public function deleteAction(Request $request, Book $book)
    {
        $form = $this->createDeleteForm($book);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $book->setDeleted(1);
            $em = $this->getDoctrine()->getManager();
            $em->persist($book);
            $em->flush();
        }

        return $this->redirectToRoute('book_index');
    }

    /**
     * Deletes a book entity.
     *
     * @Route("/admin/restore/{id}", name="book_restore")
     */
    public function restoreAction(Request $request, Book $book)
    {

        $book->setDeleted(0);
        $em = $this->getDoctrine()->getManager();
        $em->persist($book);
        $em->flush();

        return $this->redirectToRoute('book_deleted');
    }

    /**
     * Creates a form to delete a book entity.
     *
     * @param Book $book The book entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Book $book)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('book_delete', array('id' => $book->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }

    /**
     * Retrieve one random item of given class from ORM repository.
     * 
     * @param EntityManager $em    The Entity Manager instance to use
     * @param string        $class The class name to retrieve items from
     * @return object
     */ 
    function getRandomDoctrineItem(EntityManager $em, $class, $like, $limit)
    {
        static $counters = [];
        if (!isset($counters[$class])) {
            $counters[$class] = (int) $em->createQuery(
                'SELECT COUNT(c) FROM '. $class .' c' 
            )->getSingleScalarResult();
        }
        // return $counters;
        return $em
            ->createQuery('SELECT c FROM ' . $class .' c WHERE c.category = :like ORDER BY c.id ASC')
            ->setMaxResults($limit)
            ->setParameter('like', $like)
            ->setFirstResult(mt_rand(0, 5))
            ->getResult()
        ;
    }

    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

    private function find($entity, $id){
        $entity = $this->em()->getRepository("AppBundle:$entity")->find($id);
        return $entity;
    }

    private function findby($entity, $by, $actual){
        $query_string = "findBy$by";
        $entity = $this->em()->getRepository("AppBundle:$entity")->$query_string($actual);
        return $entity;
    }

    private function findandlimit($entity, $by, $actual, $limit, $order){
        $entity = $this->em()->getRepository("AppBundle:$entity")
            ->findBy(
                array($by => $actual),
                array('id' => $order),
                $limit
            );
        return $entity;
    }
    
    private function findbyandlimit($entity, $by, $actual, $by2, $actual2, $limit, $offset){
        $entity = $this->em()->getRepository("AppBundle:$entity")
            ->findBy(
                array($by => $actual, $by2 => $actual2),
                array('id' => 'ASC'),
                $limit,
                $offset
            );
        return $entity;
    }

    private function save($entity){
        $this->em()->persist($entity);
        $this->em()->flush();
        return true;
    }

    private function delete($entity){
        $this->em()->remove($entity);
        $this->em()->flush();
        return true;
    }

    private function user(){
        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        return $user;
    }

}
