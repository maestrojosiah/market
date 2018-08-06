<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Purchase;
use AppBundle\Entity\Prospect;
use AppBundle\Entity\Orda;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Cookie;

/**
 * Purchase controller.
 *
 * @Route("purchase")
 */
class PurchaseController extends Controller
{
    /**
     * Lists all purchase entities.
     *
     * @Route("/", name="purchase_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $purchases = $em->getRepository('AppBundle:Purchase')->findAll();

        return $this->render('purchase/index.html.twig', array(
            'purchases' => $purchases,
        ));
    }

    /**
     * @Route("/cart/add/cookie", name="add_to_cart")
     */
    public function addToCartAction(Request $request)
    {

        $book_id = $request->request->get('book_id');
        $agent_id = $request->request->get('user_id');
        $order_qty = $request->request->get('order_qty');

        $book = $this->em()->getRepository('AppBundle:Book')->find($book_id);
        $user = $this->em()->getRepository('AppBundle:User')->find($agent_id);

        $cookie = $this->setCookieAction($book_id, $order_qty);
        // list($cookies, $count_cookies) = $this->readCookieAction($request);
    
        // $now = date("d-m-Y h:i:s");
        // $book->setUploaded(new \DateTime($now));            

        return new JsonResponse($cookies);
    }

    /**
     * Finds and displays a purchase entity.
     *
     * @Route("/{id}", name="purchase_show")
     * @Method("GET")
     */
    public function showAction(Purchase $purchase)
    {

        return $this->render('purchase/show.html.twig', array(
            'purchase' => $purchase,
        ));
    }

    /**
     * Link to cart.
     *
     * @Route("/show/cart", name="cart")
     */
    public function cartAction(Request $request)
    {
        $books = [];
        $quantities = [];
        list($cookies, $count_cookies) = $this->readCookieAction($request);
        foreach ($cookies as $key => $cookie) {
            $cookie_key = $key;
            $quantity = explode("_", $cookie_key)[2];
            $book = $this->em()->getRepository('AppBundle:Book')->find($cookie);
            $books[] = $book;
            $quantities[$book->getId()] = $quantity;
        }
        return $this->render('cart/my_cart.html.twig', array(
            'books' => $books,
            'quantities' => $quantities,
            'cookies' => $cookies,
            'count_cookies' => $count_cookies,
        ));
    }

    /**
=     * @Route("/cart/checkout", name="checkout")
     */
    public function checkoutAction(Request $request)
    {
        $name = $request->request->get('name');
        $email = $request->request->get('email');
        $phone_number = $request->request->get('phone_number');
        $residence = $request->request->get('residence');
        $agent_id = $request->request->get('agent_id');

        $existing_prospect = $this->em()->getRepository('AppBundle:Prospect')
            ->findOneBy(
                array('email' => $email),
                array('id' => 'DESC')
            );

        if($existing_prospect){
            $prospect = $existing_prospect;
        } else {
            $prospect = new Prospect();
        }
        
        $agent = $this->find('User', $agent_id);
        $prospect->setName($name);
        $prospect->setEmail($email);
        $prospect->setPhone($phone_number);
        $prospect->setResidence($residence);
        $prospect->setUser($agent);
        $this->save($prospect);

        $order = new Orda();
        $order->setProspect($prospect);
        $this->save($order);

        list($cookies, $count_cookies) = $this->readCookieAction($request);
        $books = [];
        $quantities = [];
        foreach ($cookies as $key => $cookie) {
            $book = $this->em()->getRepository('AppBundle:Book')->find($cookie);
            $cookie_key = $key;
            $quantity = explode("_", $cookie_key)[2];
            $cost = $book->getCost() * $quantity;
            $books[] = $book;
            $quantities[$book->getId()] = $quantity;
            $purchase = new Purchase();
            $now = date("d-m-Y h:i:s");
            $purchase->setPurchasedOn(new \DateTime($now));                    
            $purchase->setBook($book);
            $purchase->setQuantity($quantity);
            $purchase->setCost($cost);
            $purchase->setProspect($prospect);
            $purchase->setOrder($order);
            $this->save($purchase);
        }

        $order_number = $order->getId();

        $from = $agent->getEmail();
        $to = $prospect->getEmail();
        $subject = "Your Order is on Its Way";

        $notif_from = $prospect->getEmail();
        $notif_to = $agent->getEmail();
        $notif_subject = "Sale From HHESKenya";
        $notif_message = $agent->getFName(). " has made a sale. His number is ". $agent->getPhone() . ". The purchase was done by ". $prospect->getName() . " from ".$residence . ". The client's number is " . $prospect->getPhone().". Please connect them in case the seller is email illiterate.";

        $this->sendReceiptAction($from, $to, $subject, $books, $prospect->getName(), $order_number, $quantities);
        $this->sendNotificationAction($notif_from, $notif_to, $notif_subject, $books, $prospect->getName(), $prospect->getPhone(), $quantities);
        // $this->sendNotificationToAdminAction('kefmo2011@gmail.com', 'kefmo2011@gmail.com', $notif_subject, $books, $prospect->getName(), $prospect->getPhone(), $notif_message);
        $this->sendNotificationToAdminAction('maestrojosiah@gmail.com', 'maestrojosiah@gmail.com', $notif_subject, $books, $prospect->getName(), $prospect->getPhone(), $notif_message);
        $this->clearCookieAction($request);

        return new JsonResponse("success");

    }

    public function setCookieAction($value, $order_qty)
    {
        $value = $value;
        $html = '<html><body>test set cookie varName =' . $value . '</body></html>';
        $response = new Response($html);          
        $response->headers->setCookie(new Cookie("book_".$value."_".$order_qty, $value, time() + (3600 * 48))); 
        $response->send();
        return $response; 
    }
    
    public function readCookieAction(Request $request)
    {
        $cookies = $request->cookies->all();
        $book_cookies = [];
        foreach ($cookies as $key => $cook) {
            if (strpos($key, 'book') !== false) {
                $book_cookies[$key] = $cook;
            }
        }
        $count_cookies = count($book_cookies);
        return [$book_cookies, $count_cookies];            
    }

    public function readCookieFullAction(Request $request)
    {
        $cookies = $request->cookies->all();
        $book_cookies = [];
        foreach ($cookies as $key => $cook) {
            if (strpos($key, 'book') !== false) {
                $book_cookies[] = [$key => $cook];
            }
        }
        $count_cookies = count($book_cookies);
        return [$book_cookies, $count_cookies];            
    }

    /**
     * @Route("/clear/cart", name="clear_cart")
     */
    public function clearCookieAction(Request $request)
    {
        $list  = [];
        $cookies = $this->readCookieFullAction($request);
        if (isset($_SERVER['HTTP_COOKIE'])) {
            $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
            foreach ($cookies as $key => $cook) {
                $parts = explode('=', $cook);
                $name = trim($parts[0]);
                if (strpos($name, 'book') !== false) {
                    setcookie($name, '', time()-1000);
                    setcookie($name, '', time()-1000, '/');
                    $list[] = $name;
                }
                
            }
        }        
        return new JsonResponse($list);
    }

    private function em(){
        $em = $this->getDoctrine()->getManager();
        return $em;
    }

    private function find($entity, $id){
        $entity = $this->em()->getRepository("AppBundle:$entity")->find($id);
        return $entity;
    }

    private function save($entity){
        $this->em()->persist($entity);
        $this->em()->flush();
        return true;
    }

    /**
     * @Route("/send_receipt/{from}/{to}/{subject/{message}")
     */
    public function sendReceiptAction($from, $to, $subject, $books, $prospect_name, $order_number, $quantities)
    {

        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($this->renderView('mail/receipt.html.twig', ['books'=>$books, 'prospect'=>$prospect_name, 'order_number'=>$order_number, 'quantities'=>$quantities]), 'text/html')
        ;
        $mailer->send($message);
        return new Response('<html><body>The email has been sent successfully!</body></html>');
    }

    public function sendNotificationAction($from, $to, $subject, $books, $prospect_name, $phone_number, $quantities)
    {

        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($this->renderView('mail/notification.html.twig', ['books'=>$books, 'prospect'=>$prospect_name, 'phone_number'=>$phone_number, 'quantities'=>$quantities]), 'text/html')
        ;
        $mailer->send($message);
        return new Response('<html><body>The email has been sent successfully!</body></html>');
    }

    public function sendNotificationToAdminAction($from, $to, $subject, $books, $prospect_name, $phone_number, $message)
    {

        $mailer = $this->get('mailer');

        $message = \Swift_Message::newInstance()
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo($to)
            ->setBody($message);
        ;
        $mailer->send($message);
        return new Response('<html><body>The email has been sent successfully!</body></html>');
    }


}
